<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Export / import / template CSV untuk tabel statistik yang dikunci PERIODE,
 * bukan wilayah — pasangannya CsvPerKecamatan yang mencocokkan baris pada
 * (kecamatan + tahun).
 *
 * Di sini kunci pencocokannya bebas, mis. ['tahun'] untuk ringkasan tahunan
 * atau ['tahun', 'kode_sektor'] untuk rincian lapangan usaha. Baris yang
 * kuncinya sudah ada diperbarui, yang belum dibuat.
 */
trait CsvPerPeriode
{
    /** Nama berkas tanpa ekstensi, mis. 'perekonomian'. */
    abstract protected function csvNama(): string;

    /** FQCN model tujuan. */
    abstract protected function csvModel(): string;

    /** Kolom pembentuk kunci unik; semuanya diperlakukan sebagai bilangan bulat. */
    abstract protected function csvKunci(): array;

    /** [nama_kolom => 'int'|'desimal'|'teks'] untuk kolom data. */
    abstract protected function csvKolom(): array;

    /** Nama route tujuan setelah impor. */
    abstract protected function csvRedirect(): string;

    /**
     * Kolom yang WAJIB terisi saat baris baru dibuat, karena di database-nya
     * NOT NULL tanpa nilai bawaan. Saat memperbarui baris lama, kolom kosong
     * tetap boleh dilewati.
     */
    protected function csvKolomWajib(): array
    {
        return array_keys($this->csvKolom());
    }

    /** Contoh nilai per kolom untuk berkas template. */
    protected function csvContoh(): array
    {
        return [];
    }

    private function csvHeader(): array
    {
        return array_merge($this->csvKunci(), array_keys($this->csvKolom()));
    }

    public function template()
    {
        $contoh = $this->csvContoh();

        $baris = [];
        foreach ($this->csvHeader() as $kolom) {
            $baris[] = $contoh[$kolom] ?? ($kolom === 'tahun' ? now()->year : 0);
        }

        return $this->streamCsv("template-{$this->csvNama()}.csv", [$baris]);
    }

    public function export()
    {
        $model = $this->csvModel();
        $query = $model::query();

        foreach ($this->csvKunci() as $kunci) {
            $query->orderBy($kunci);
        }

        $rows = $query->get()->map(function ($r) {
            $baris = [];
            foreach ($this->csvHeader() as $kolom) {
                $baris[] = $r->{$kolom};
            }

            return $baris;
        })->all();

        return $this->streamCsv("{$this->csvNama()}-" . now()->format('Ymd-His') . '.csv', $rows);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Gagal membaca file.');
        }

        // Excel lokal kadang menulis CSV dengan pemisah titik-koma.
        $barisPertama = fgets($handle);
        $pemisah      = substr_count((string) $barisPertama, ';') > substr_count((string) $barisPertama, ',') ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 0, $pemisah);
        if ($header === false) {
            fclose($handle);

            return back()->with('error', 'File kosong atau format tidak valid.');
        }

        // Buang BOM dan samakan huruf besar/kecil nama kolom.
        $header = array_map(fn ($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $h))), $header);
        $col    = array_flip($header);

        foreach ($this->csvKunci() as $wajib) {
            if (!isset($col[$wajib])) {
                fclose($handle);

                return back()->with('error', "Kolom '{$wajib}' tidak ditemukan di header CSV. Unduh template untuk format yang benar.");
            }
        }

        $gagal = [];
        $baris = 1; // baris header

        // Baris yang formatnya salah dicatat lalu dilewati; tapi kalau terjadi
        // kegagalan tak terduga di tengah jalan, seluruh impor dibatalkan agar
        // tidak menyisakan data separuh jadi.
        try {
            $sukses = DB::transaction(function () use ($handle, $pemisah, $col, &$gagal, &$baris) {
                $model  = $this->csvModel();
                $defs   = $this->csvKolom();
                $jumlah = 0;

                while (($data = fgetcsv($handle, 0, $pemisah)) !== false) {
                    $baris++;
                    if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                        continue;
                    }

                    $ambil = fn ($key) => isset($col[$key], $data[$col[$key]]) ? trim((string) $data[$col[$key]]) : null;

                    $kunci = [];
                    foreach ($this->csvKunci() as $kolom) {
                        $isi = $ambil($kolom);
                        if ($isi === null || $isi === '' || !is_numeric($isi)) {
                            $gagal[] = "Baris {$baris}: kolom '{$kolom}' kosong atau bukan angka";
                            continue 2;
                        }
                        $kunci[$kolom] = (int) $isi;
                    }

                    if (isset($kunci['tahun']) && ($kunci['tahun'] < 1900 || $kunci['tahun'] > 2100)) {
                        $gagal[] = "Baris {$baris}: tahun tidak valid";
                        continue;
                    }

                    $nilai = [];
                    foreach ($defs as $kolom => $tipe) {
                        $isi = $ambil($kolom);
                        if ($isi === null || $isi === '') {
                            continue; // kolom kosong dibiarkan apa adanya
                        }
                        $nilai[$kolom] = match ($tipe) {
                            'teks'    => $isi,
                            'desimal' => (float) str_replace(',', '.', $isi),
                            default   => (int) preg_replace('/[^\d-]/', '', $isi),
                        };
                    }

                    if ($nilai === []) {
                        $gagal[] = "Baris {$baris}: tidak ada kolom data yang terisi";
                        continue;
                    }

                    // Baris baru tidak boleh dibuat setengah jadi: kolom NOT NULL
                    // yang belum terisi akan ditolak database, jadi dicegat di sini
                    // dengan pesan yang bisa ditindaklanjuti.
                    $adaSebelumnya = $model::where($kunci)->exists();
                    if (!$adaSebelumnya) {
                        $kurang = array_diff($this->csvKolomWajib(), array_keys($nilai));
                        if ($kurang !== []) {
                            $gagal[] = "Baris {$baris}: baris baru butuh kolom " . implode(', ', $kurang);
                            continue;
                        }
                    }

                    $model::updateOrCreate($kunci, $nilai);
                    $jumlah++;
                }

                return $jumlah;
            });
        } catch (\Throwable $e) {
            fclose($handle);
            report($e);

            return back()->with('error', 'Impor dibatalkan karena terjadi kesalahan; tidak ada data yang berubah.');
        }

        fclose($handle);

        $pesan = "{$sukses} baris berhasil diimpor.";
        if ($gagal !== []) {
            $pesan .= ' ' . count($gagal) . ' baris dilewati: ' . implode('; ', array_slice($gagal, 0, 5));
            if (count($gagal) > 5) {
                $pesan .= '; ...';
            }
        }

        return redirect()->route($this->csvRedirect())
            ->with($sukses > 0 ? 'success' : 'error', $pesan);
    }

    /** Stream baris ke unduhan CSV; BOM dipasang agar Excel membacanya sebagai UTF-8. */
    private function streamCsv(string $namaBerkas, array $rows)
    {
        $header = $this->csvHeader();

        return response()->streamDownload(function () use ($rows, $header) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $header);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
