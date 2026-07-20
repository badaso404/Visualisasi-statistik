<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataBencana;
use App\Services\SatuDataBencanaSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kelola rekap bencana triwulanan Jakarta Barat.
 *
 * Sumber utamanya API Satu Data Jakarta (granularitas: kota/kabupaten per
 * triwulan per jenis bencana). Tabel lokal berperan sebagai cermin API agar
 * halaman publik tidak bergantung pada API saat render, sekaligus memungkinkan
 * admin menambah/mengoreksi data secara manual.
 */
class BencanaController extends Controller
{
    /** Kolom CSV (urutan tetap untuk export, template, & import). */
    private const CSV_COLUMNS = [
        'tahun', 'triwulan', 'jenis_bencana', 'jumlah_kejadian',
        'jumlah_korban_meninggal', 'jumlah_korban_luka', 'keterangan', 'sumber',
    ];

    /** Query dasar: hanya baris rekap Jakarta Barat. */
    private function rekap()
    {
        return DataBencana::whereNotNull('periode_data')
            ->where('wilayah', DataBencana::WILAYAH_JAKBAR);
    }

    /** "202403" dari tahun + triwulan (TW1→03, TW2→06, TW3→09, TW4→12). */
    private function periodeDari(int $tahun, int $triwulan): string
    {
        return sprintf('%04d%02d', $tahun, $triwulan * 3);
    }

    public function index()
    {
        $items = $this->rekap()
            ->orderByDesc('periode_data')
            ->orderBy('jenis_bencana')
            ->get();

        $jenisList = DataBencana::JENIS;

        // Titik peta bencana dikelola di tab kedua halaman ini
        $titik = \App\Models\TitikBencana::with('kecamatan')
            ->orderBy('kategori')->orderBy('level')->orderBy('nama')->get();
        $kategoriList = \App\Models\TitikBencana::KATEGORI;
        $kecamatan = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.bencana.index', compact(
            'items', 'jenisList', 'titik', 'kategoriList', 'kecamatan'
        ));
    }

    /** Tarik ulang rekap dari API Satu Data Jakarta. */
    public function sync(SatuDataBencanaSync $sync)
    {
        $h = $sync->jalankan();

        if ($h['error']) {
            return back()->with('error', 'Sync gagal: ' . $h['error']);
        }

        return back()->with('success', sprintf(
            'Sync dari Satu Data Jakarta selesai — %d ditambah, %d diperbarui (%d baris wilayah lain dilewati).',
            $h['ditambah'], $h['diperbarui'], $h['dilewati']
        ));
    }

    public function store(Request $request)
    {
        DataBencana::create($this->validated($request));

        return redirect()->route('admin.bencana.index')->with('success', 'Data rekap bencana ditambahkan.');
    }

    public function update(Request $request, DataBencana $bencana)
    {
        $bencana->update($this->validated($request));

        return redirect()->route('admin.bencana.index')->with('success', 'Data rekap bencana diperbarui.');
    }

    public function destroy(DataBencana $bencana)
    {
        $bencana->delete();

        return redirect()->route('admin.bencana.index')->with('success', 'Data rekap bencana dihapus.');
    }

    /** Export rekap ke CSV (bisa dibuka di Excel). */
    public function export()
    {
        $items = $this->rekap()->orderByDesc('periode_data')->orderBy('jenis_bencana')->get();

        return response()->streamDownload(function () use ($items) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8
            fputcsv($out, self::CSV_COLUMNS);
            foreach ($items as $b) {
                fputcsv($out, [
                    $b->tahun,
                    $b->triwulan,
                    $b->jenis_bencana,
                    $b->jumlah_kejadian,
                    $b->jumlah_korban_meninggal,
                    $b->jumlah_korban_luka,
                    $b->keterangan,
                    $b->sumber,
                ]);
            }
            fclose($out);
        }, 'rekap-bencana-' . date('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Unduh template CSV kosong + 1 baris contoh. */
    public function template()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::CSV_COLUMNS);
            fputcsv($out, ['2026', '1', 'Banjir', '63', '0', '0', 'Contoh keterangan', 'Satu Data Jakarta']);
            fclose($out);
        }, 'template-rekap-bencana.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Import rekap dari file CSV. */
    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'max:5120']]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return back()->with('error', 'Format harus CSV. Kalau file dari Excel (.xlsx), buka lalu "Save As / Simpan Sebagai" CSV dulu.');
        }

        $content = file_get_contents($request->file('file')->getRealPath());
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content); // buang BOM
        $lines = preg_split('/\r\n|\r|\n/', trim((string) $content));
        if (count($lines) < 2) {
            return back()->with('error', 'File kosong atau tidak ada baris data.');
        }

        // Deteksi pemisah (Excel ID sering pakai ;)
        $delim = substr_count($lines[0], ';') > substr_count($lines[0], ',') ? ';' : ',';
        $header = array_map(fn($h) => strtolower(trim($h)), str_getcsv(array_shift($lines), $delim));

        // Impor bersifat semua-atau-tidak sama sekali supaya kegagalan di
        // tengah berkas tidak menyisakan data separuh jadi.
        $created = 0; $updated = 0; $skipped = 0;
        try {
            DB::transaction(function () use ($lines, $delim, $header, &$created, &$updated, &$skipped) {
                foreach ($lines as $line) {
                    if (trim($line) === '') continue;
                    $row = str_getcsv($line, $delim);
                    $get = function ($col) use ($row, $header) {
                        $i = array_search($col, $header);
                        return $i !== false && isset($row[$i]) ? trim($row[$i]) : null;
                    };

                    $jenis    = $get('jenis_bencana');
                    $tahun    = (int) $get('tahun');
                    $triwulan = (int) $get('triwulan');
                    if (!$jenis || $tahun < 2000 || $triwulan < 1 || $triwulan > 4) { $skipped++; continue; }

                    $rec = DataBencana::updateOrCreate(
                        [
                            'periode_data'  => $this->periodeDari($tahun, $triwulan),
                            'wilayah'       => DataBencana::WILAYAH_JAKBAR,
                            'jenis_bencana' => $jenis,
                        ],
                        [
                            'tahun'                   => $tahun,
                            'triwulan'                => $triwulan,
                            'jumlah_kejadian'         => (int) ($get('jumlah_kejadian') ?: 0),
                            'jumlah_korban_meninggal' => (int) ($get('jumlah_korban_meninggal') ?: 0),
                            'jumlah_korban_luka'      => (int) ($get('jumlah_korban_luka') ?: 0),
                            'keterangan'              => $get('keterangan') ?: null,
                            'sumber'                  => $get('sumber') ?: null,
                        ]
                    );
                    $rec->wasRecentlyCreated ? $created++ : $updated++;
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Impor dibatalkan karena terjadi kesalahan; tidak ada data yang berubah.');
        }

        return back()->with('success', "Import selesai — $created ditambah, $updated diperbarui, $skipped baris dilewati (tahun/triwulan/jenis tidak valid).");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'tahun'                   => ['required', 'integer', 'min:2000', 'max:2100'],
            'triwulan'                => ['required', 'integer', 'between:1,4'],
            'jenis_bencana'           => ['required', 'string', 'max:255'],
            'jumlah_kejadian'         => ['required', 'integer', 'min:0'],
            'jumlah_korban_meninggal' => ['required', 'integer', 'min:0'],
            'jumlah_korban_luka'      => ['required', 'integer', 'min:0'],
            'keterangan'              => ['nullable', 'string'],
            'sumber'                  => ['nullable', 'string', 'max:255'],
        ]);

        // Periode & wilayah diturunkan otomatis agar konsisten dengan format API
        $data['periode_data'] = $this->periodeDari($data['tahun'], $data['triwulan']);
        $data['wilayah']      = DataBencana::WILAYAH_JAKBAR;

        return $data;
    }
}
