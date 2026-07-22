<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Export / import / template CSV untuk tabel "per kecamatan per tahun".
 *
 * Wajib dipakai bersama IsiMassalPerKecamatan — definisi kolom diambil dari
 * batchFields() yang sudah ada di sana, jadi kolom CSV selalu ikut kalau
 * daftar kolom berubah.
 *
 * Baris dicocokkan pada (kecamatan + tahun): sudah ada -> diperbarui, belum -> dibuat.
 */
trait CsvPerKecamatan
{
    /** Nama berkas tanpa ekstensi, mis. 'penduduk-kecamatan'. */
    abstract protected function csvNama(): string;

    /** Urutan kolom CSV: kecamatan, tahun, lalu kolom-kolom data. */
    private function csvHeader(): array
    {
        return array_merge(['kecamatan', 'tahun'], array_keys($this->batchFieldsNormal()));
    }

    public function template()
    {
        $contoh = array_merge(
            [Kecamatan::orderBy('nama_kecamatan')->value('nama_kecamatan') ?: 'Cakung', now()->year],
            array_fill(0, count($this->batchFieldsNormal()), 0),
        );

        return $this->streamCsv("template-{$this->csvNama()}.csv", [$contoh]);
    }

    public function export()
    {
        $model  = $this->batchModel();
        $fields = array_keys($this->batchFieldsNormal());

        $rows = $model::with('kecamatan')
            ->orderBy('tahun')
            ->get()
            ->map(function ($r) use ($fields) {
                $baris = [$r->kecamatan->nama_kecamatan ?? '', $r->tahun];
                foreach ($fields as $f) {
                    $baris[] = $r->{$f};
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
        $pemisah      = substr_count($barisPertama, ';') > substr_count($barisPertama, ',') ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 0, $pemisah);
        if ($header === false) {
            fclose($handle);

            return back()->with('error', 'File kosong atau format tidak valid.');
        }

        // Buang BOM dan samakan huruf besar/kecil nama kolom.
        $header = array_map(fn ($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $h))), $header);
        $col    = array_flip($header);

        foreach (['kecamatan', 'tahun'] as $wajib) {
            if (!isset($col[$wajib])) {
                fclose($handle);

                return back()->with('error', "Kolom '{$wajib}' tidak ditemukan di header CSV. Unduh template untuk format yang benar.");
            }
        }

        $model          = $this->batchModel();
        $defs           = $this->batchFieldsNormal();
        $kecamatanByNama = Kecamatan::all()->keyBy(fn ($k) => strtolower(trim($k->nama_kecamatan)));

        $gagal = [];
        $baris = 1; // baris header

        // Baris yang formatnya salah dicatat lalu dilewati; tapi kalau terjadi
        // kegagalan tak terduga di tengah jalan, seluruh impor dibatalkan agar
        // tidak menyisakan data separuh jadi.
        try {
            $sukses = DB::transaction(function () use ($handle, $pemisah, $col, $defs, $model, $kecamatanByNama, &$gagal, &$baris) {
                $jumlah = 0;

                while (($data = fgetcsv($handle, 0, $pemisah)) !== false) {
                    $baris++;
                    if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                        continue;
                    }

                    $ambil = fn ($key) => isset($col[$key], $data[$col[$key]]) ? trim((string) $data[$col[$key]]) : null;

                    $kec = $kecamatanByNama->get(strtolower((string) $ambil('kecamatan')));
                    if (!$kec) {
                        $gagal[] = "Baris {$baris}: kecamatan '" . $ambil('kecamatan') . "' tidak dikenal";
                        continue;
                    }

                    $tahun = (int) $ambil('tahun');
                    if ($tahun < 1900 || $tahun > 2100) {
                        $gagal[] = "Baris {$baris}: tahun tidak valid";
                        continue;
                    }

                    // Baris untuk tahun yang belum punya ringkasan induk tidak
                    // akan pernah tampil di situs publik, jadi lebih baik ditolak
                    // dengan pesan daripada tersimpan diam-diam lalu hilang.
                    if (!$this->tahunPunyaInduk($tahun)) {
                        $gagal[] = "Baris {$baris}: belum ada " . $this->sebutanInduk() . " tahun {$tahun}";
                        continue;
                    }

                    $nilai = [];
                    foreach ($defs as $field => $def) {
                        $isi = $ambil($field);
                        if ($isi === null || $isi === '') {
                            continue; // kolom kosong dibiarkan apa adanya
                        }
                        $nilai[$field] = $def['desimal']
                            ? (float) str_replace(',', '.', $isi)
                            : (int) preg_replace('/[^\d-]/', '', $isi);
                    }

                    if ($nilai === []) {
                        $gagal[] = "Baris {$baris}: tidak ada kolom data yang terisi";
                        continue;
                    }

                    $model::updateOrCreate(
                        ['kecamatan_id' => $kec->id, 'tahun' => $tahun],
                        $nilai,
                    );
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

        return redirect()->route($this->batchRedirect())
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
