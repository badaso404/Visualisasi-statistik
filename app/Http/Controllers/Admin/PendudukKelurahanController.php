<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\PendudukKelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendudukKelurahanController extends Controller
{
    use ValidasiPeriodeUnik;

    public function store(Request $request)
    {
        PendudukKelurahan::create($this->validated($request));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kelurahan ditambahkan.');
    }

    public function update(Request $request, PendudukKelurahan $pendudukKelurahan)
    {
        $pendudukKelurahan->update($this->validated($request, $pendudukKelurahan));

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kelurahan diperbarui.');
    }

    public function destroy(PendudukKelurahan $pendudukKelurahan)
    {
        $pendudukKelurahan->delete();

        return redirect()->route('admin.kependudukan.index')->with('success', 'Data penduduk kelurahan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'    => ['required', 'exists:kecamatan,id'],
            'tahun'           => ['required', 'integer', 'min:1900', 'max:2100'],
            'nama_kelurahan'  => ['required', 'string', 'max:255',
                $this->unikPerPeriode('penduduk_kelurahan', ['tahun' => $request->input('tahun')], $item),
            ],
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
            'jumlah_penduduk' => ['required', 'integer', 'min:0'],
        ], $this->pesanPeriodeUnik('kelurahan ini untuk tahun tersebut'));
    }

    /** Kolom CSV yang dipakai untuk import/export/template. */
    private const CSV_HEADER = ['kecamatan', 'tahun', 'nama_kelurahan', 'latitude', 'longitude', 'jumlah_penduduk'];

    /** Unduh template CSV kosong (header + 1 baris contoh). */
    public function template()
    {
        return $this->streamCsv('template-penduduk-kelurahan.csv', [
            ['Kebon Jeruk', 2024, 'Kelapa Dua', -6.209248, 106.768570, 52000],
        ]);
    }

    /** Ekspor seluruh data kelurahan sebagai CSV (backup / bahan edit). */
    public function export()
    {
        $rows = PendudukKelurahan::with('kecamatan')
            ->orderBy('tahun')->orderBy('nama_kelurahan')
            ->get()
            ->map(fn ($r) => [
                $r->kecamatan->nama_kecamatan ?? '',
                $r->tahun,
                $r->nama_kelurahan,
                $r->latitude,
                $r->longitude,
                $r->jumlah_penduduk,
            ])->all();

        return $this->streamCsv('penduduk-kelurahan-' . now()->format('Ymd-His') . '.csv', $rows);
    }

    /** Import CSV → update/insert berdasarkan (nama_kelurahan + tahun). Lat/lng ikut diperbarui. */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Gagal membaca file.');
        }

        // Deteksi delimiter dari baris pertama (Excel lokal kadang pakai ;)
        $firstLine = fgets($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        // Baca header, buang BOM, normalisasi nama kolom → index
        $header = fgetcsv($handle, 0, $delimiter);
        if ($header === false) {
            fclose($handle);
            return back()->with('error', 'File kosong atau format tidak valid.');
        }
        $header = array_map(fn ($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $h))), $header);
        $col = array_flip($header);

        foreach (['kecamatan', 'tahun', 'nama_kelurahan', 'jumlah_penduduk'] as $wajib) {
            if (!isset($col[$wajib])) {
                fclose($handle);
                return back()->with('error', "Kolom '$wajib' tidak ditemukan di header CSV. Unduh template untuk format yang benar.");
            }
        }

        $kecamatanByNama = Kecamatan::all()->keyBy(fn ($k) => strtolower(trim($k->nama_kecamatan)));

        $gagal = [];
        $baris = 1; // header

        // Impor bersifat semua-atau-tidak sama sekali supaya kegagalan di
        // tengah berkas tidak menyisakan data separuh jadi.
        try {
            $sukses = DB::transaction(function () use ($handle, $delimiter, $col, $kecamatanByNama, &$gagal, &$baris) {
                $jumlah = 0;

                while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $baris++;
                    if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                        continue; // baris kosong
                    }

                    $get = fn ($key) => isset($col[$key], $data[$col[$key]]) ? trim((string) $data[$col[$key]]) : null;

                    $kec = $kecamatanByNama->get(strtolower((string) $get('kecamatan')));
                    if (!$kec) {
                        $gagal[] = "Baris {$baris}: kecamatan '" . $get('kecamatan') . "' tidak dikenal";
                        continue;
                    }

                    $namaKel = $get('nama_kelurahan');
                    $tahun   = (int) $get('tahun');
                    if ($namaKel === '' || $tahun < 1900) {
                        $gagal[] = "Baris {$baris}: nama_kelurahan/tahun tidak valid";
                        continue;
                    }

                    $lat = $get('latitude');
                    $lng = $get('longitude');

                    PendudukKelurahan::updateOrCreate(
                        ['nama_kelurahan' => $namaKel, 'tahun' => $tahun],
                        [
                            'kecamatan_id'    => $kec->id,
                            'latitude'        => ($lat === '' || $lat === null) ? null : (float) str_replace(',', '.', $lat),
                            'longitude'       => ($lng === '' || $lng === null) ? null : (float) str_replace(',', '.', $lng),
                            'jumlah_penduduk' => (int) preg_replace('/[^\d]/', '', (string) $get('jumlah_penduduk')),
                        ],
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

        $pesan = "$sukses baris berhasil diimpor.";
        if (!empty($gagal)) {
            $pesan .= ' ' . count($gagal) . ' baris dilewati: ' . implode('; ', array_slice($gagal, 0, 5));
            if (count($gagal) > 5) {
                $pesan .= '; ...';
            }
        }

        return redirect()->route('admin.kependudukan.index')
            ->with($sukses > 0 ? 'success' : 'error', $pesan);
    }

    /** Helper: stream array baris ke unduhan CSV (dengan BOM agar Excel baca UTF-8). */
    private function streamCsv(string $filename, array $rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM
            fputcsv($out, self::CSV_HEADER);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
