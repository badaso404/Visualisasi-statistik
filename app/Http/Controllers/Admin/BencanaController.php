<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataBencana;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BencanaController extends Controller
{
    public function index()
    {
        $items = DataBencana::with('kecamatan')
            ->orderByDesc('tahun')
            ->orderByDesc('tanggal_kejadian')
            ->get();

        $kecamatan = Kecamatan::orderBy('nama_kecamatan')->get();
        $jenisList = DataBencana::JENIS;

        return view('admin.bencana.index', compact('items', 'kecamatan', 'jenisList'));
    }

    public function store(Request $request)
    {
        DataBencana::create($this->validated($request));

        return redirect()->route('admin.bencana.index')->with('success', 'Data bencana ditambahkan.');
    }

    public function update(Request $request, DataBencana $bencana)
    {
        $bencana->update($this->validated($request));

        return redirect()->route('admin.bencana.index')->with('success', 'Data bencana diperbarui.');
    }

    public function destroy(DataBencana $bencana)
    {
        $bencana->delete();

        return redirect()->route('admin.bencana.index')->with('success', 'Data bencana dihapus.');
    }

    /** Kolom CSV (urutan tetap untuk export & template). */
    private const CSV_COLUMNS = [
        'tanggal_kejadian', 'jenis_bencana', 'nama_lokasi', 'kecamatan', 'tahun',
        'latitude', 'longitude', 'jumlah_kejadian', 'jumlah_korban', 'jumlah_terdampak',
        'keterangan', 'sumber',
    ];

    /** Normalkan koordinat CSV: buang koma nyasar / ubah koma desimal jadi titik. */
    private function normalisasiKoordinat($v): string
    {
        $v = trim((string) $v);
        return str_contains($v, '.') ? str_replace(',', '', $v) : str_replace(',', '.', $v);
    }

    /** Export semua data bencana ke CSV (bisa dibuka di Excel). */
    public function export()
    {
        $items = DataBencana::with('kecamatan')
            ->orderByDesc('tahun')->orderByDesc('tanggal_kejadian')->get();

        return response()->streamDownload(function () use ($items) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8
            fputcsv($out, self::CSV_COLUMNS);
            foreach ($items as $b) {
                fputcsv($out, [
                    $b->tanggal_kejadian ? \Carbon\Carbon::parse($b->tanggal_kejadian)->format('Y-m-d') : '',
                    $b->jenis_bencana,
                    $b->nama_lokasi,
                    $b->kecamatan->nama_kecamatan ?? '',
                    $b->tahun,
                    $b->latitude,
                    $b->longitude,
                    $b->jumlah_kejadian,
                    $b->jumlah_korban,
                    $b->jumlah_terdampak,
                    $b->keterangan,
                    $b->sumber,
                ]);
            }
            fclose($out);
        }, 'data-bencana-' . date('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Unduh template CSV kosong + 1 baris contoh. */
    public function template()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::CSV_COLUMNS);
            fputcsv($out, [
                '2026-01-15', 'Banjir', 'Kel. Kapuk', 'Cengkareng', '2026',
                '-6.1466', '106.7380', '12', '0', '3500', 'Contoh keterangan', 'BPBD DKI Jakarta',
            ]);
            fclose($out);
        }, 'template-data-bencana.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Import data bencana dari file CSV. */
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

        $kecMap = Kecamatan::all()->keyBy(fn($k) => strtolower(trim($k->nama_kecamatan)));

        // Impor bersifat semua-atau-tidak sama sekali supaya kegagalan di
        // tengah berkas tidak menyisakan data separuh jadi.
        $created = 0; $updated = 0; $skipped = 0;
        try {
            DB::transaction(function () use ($lines, $delim, $header, $kecMap, &$created, &$updated, &$skipped) {
                foreach ($lines as $line) {
                    if (trim($line) === '') continue;
                    $row = str_getcsv($line, $delim);
                    $get = function ($col) use ($row, $header) {
                        $i = array_search($col, $header);
                        return $i !== false && isset($row[$i]) ? trim($row[$i]) : null;
                    };

                    $jenis  = $get('jenis_bencana');
                    $lokasi = $get('nama_lokasi');
                    $tahun  = $get('tahun');
                    if (!$jenis || !$lokasi || !$tahun) { $skipped++; continue; }

                    $tgl = $get('tanggal_kejadian');
                    try {
                        $tglParsed = $tgl ? \Carbon\Carbon::parse($tgl)->format('Y-m-d') : null;
                    } catch (\Throwable $e) {
                        $tglParsed = null;
                    }

                    $kecName = strtolower((string) $get('kecamatan'));
                    $lat = $this->normalisasiKoordinat($get('latitude'));
                    $lng = $this->normalisasiKoordinat($get('longitude'));

                    $rec = DataBencana::updateOrCreate(
                        [
                            'jenis_bencana'    => $jenis,
                            'nama_lokasi'      => $lokasi,
                            'tahun'            => (int) $tahun,
                            'tanggal_kejadian' => $tglParsed,
                        ],
                        [
                            'kecamatan_id'     => isset($kecMap[$kecName]) ? $kecMap[$kecName]->id : null,
                            'latitude'         => is_numeric($lat) ? $lat : null,
                            'longitude'        => is_numeric($lng) ? $lng : null,
                            'jumlah_kejadian'  => (int) ($get('jumlah_kejadian') ?: 1),
                            'jumlah_korban'    => (int) ($get('jumlah_korban') ?: 0),
                            'jumlah_terdampak' => (int) ($get('jumlah_terdampak') ?: 0),
                            'keterangan'       => $get('keterangan') ?: null,
                            'sumber'           => $get('sumber') ?: null,
                        ]
                    );
                    $rec->wasRecentlyCreated ? $created++ : $updated++;
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Impor dibatalkan karena terjadi kesalahan; tidak ada data yang berubah.');
        }

        return back()->with('success', "Import selesai — $created ditambah, $updated diperbarui, $skipped baris dilewati (data wajib kosong).");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'kecamatan_id'     => ['nullable', 'exists:kecamatan,id'],
            'jenis_bencana'    => ['required', 'string', 'max:255'],
            'nama_lokasi'      => ['required', 'string', 'max:255'],
            'tahun'            => ['required', 'integer', 'min:1900', 'max:2100'],
            'tanggal_kejadian' => ['nullable', 'date'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'jumlah_kejadian'  => ['required', 'integer', 'min:0'],
            'jumlah_korban'    => ['required', 'integer', 'min:0'],
            'jumlah_terdampak' => ['required', 'integer', 'min:0'],
            'keterangan'       => ['nullable', 'string'],
            'sumber'           => ['nullable', 'string', 'max:255'],
        ]);

        // Kecamatan opsional kosong → null (kolom integer tidak menerima string kosong)
        $data['kecamatan_id'] = ($data['kecamatan_id'] ?? '') !== '' ? $data['kecamatan_id'] : null;

        return $data;
    }
}
