<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TitikBencana;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TitikBencanaController extends Controller
{
    public function index()
    {
        $items = TitikBencana::with('kecamatan')
            ->orderBy('kategori')
            ->orderBy('level')
            ->orderBy('nama')
            ->get();

        return view('admin.titik-bencana.index', compact('items'));
    }

    public function create()
    {
        return view('admin.titik-bencana.form', [
            'item'         => new TitikBencana(),
            'kecamatan'    => Kecamatan::orderBy('nama_kecamatan')->get(),
            'kategoriList' => TitikBencana::KATEGORI,
        ]);
    }

    public function store(Request $request)
    {
        TitikBencana::create($this->validated($request));

        return redirect()->route('admin.titik-bencana.index')->with('success', 'Titik bencana ditambahkan.');
    }

    public function edit(TitikBencana $titikBencana)
    {
        return view('admin.titik-bencana.form', [
            'item'         => $titikBencana,
            'kecamatan'    => Kecamatan::orderBy('nama_kecamatan')->get(),
            'kategoriList' => TitikBencana::KATEGORI,
        ]);
    }

    public function update(Request $request, TitikBencana $titikBencana)
    {
        $titikBencana->update($this->validated($request));

        return redirect()->route('admin.titik-bencana.index')->with('success', 'Titik bencana diperbarui.');
    }

    public function destroy(TitikBencana $titikBencana)
    {
        $titikBencana->delete();

        return redirect()->route('admin.titik-bencana.index')->with('success', 'Titik bencana dihapus.');
    }

    /** Kolom CSV (urutan tetap untuk export & template). */
    private const CSV_COLUMNS = [
        'kategori', 'level', 'nama', 'kecamatan', 'latitude', 'longitude', 'link_maps', 'keterangan',
    ];

    /**
     * Normalkan nilai koordinat dari CSV:
     * - ada titik + koma (mis. "-6.1731,")  → koma dibuang        → "-6.1731"
     * - hanya koma (mis. "-6,1731")          → koma jadi titik      → "-6.1731"
     */
    private function normalisasiKoordinat($v): string
    {
        $v = trim((string) $v);
        if (str_contains($v, '.')) {
            return str_replace(',', '', $v);   // koma nyasar / pemisah ribuan
        }
        return str_replace(',', '.', $v);      // koma desimal
    }

    /** Export semua titik bencana ke CSV. */
    public function export()
    {
        $items = TitikBencana::with('kecamatan')
            ->orderBy('kategori')->orderBy('level')->orderBy('nama')->get();

        return response()->streamDownload(function () use ($items) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::CSV_COLUMNS);
            foreach ($items as $t) {
                fputcsv($out, [
                    $t->kategori,
                    $t->level,
                    $t->nama,
                    $t->kecamatan->nama_kecamatan ?? '',
                    $t->latitude,
                    $t->longitude,
                    $t->link_maps,
                    $t->keterangan,
                ]);
            }
            fclose($out);
        }, 'titik-bencana-' . date('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Unduh template CSV kosong + baris contoh. */
    public function template()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::CSV_COLUMNS);
            fputcsv($out, ['banjir_rawan', '1', 'Kapuk - Kamal Muara', 'Cengkareng', '-6.1751', '106.7272', '', 'Sering tergenang > 50 cm']);
            fputcsv($out, ['pos_damkar', '', 'Pos Damkar Cengkareng', 'Cengkareng', '-6.1950', '106.7100', 'https://maps.app.goo.gl/xxxx', 'Siaga 24 jam']);
            fclose($out);
        }, 'template-titik-bencana.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Import titik bencana dari file CSV. */
    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'max:5120']]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return back()->with('error', 'Format harus CSV. Kalau file dari Excel (.xlsx), buka lalu "Save As / Simpan Sebagai" CSV dulu.');
        }

        $content = file_get_contents($request->file('file')->getRealPath());
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', trim((string) $content));
        if (count($lines) < 2) {
            return back()->with('error', 'File kosong atau tidak ada baris data.');
        }

        $delim = substr_count($lines[0], ';') > substr_count($lines[0], ',') ? ';' : ',';
        $header = array_map(fn($h) => strtolower(trim($h)), str_getcsv(array_shift($lines), $delim));

        $kecMap = Kecamatan::all()->keyBy(fn($k) => strtolower(trim($k->nama_kecamatan)));

        // Peta kategori: terima key (pos_damkar) maupun label ("Pos Damkar")
        $katMap = [];
        foreach (TitikBencana::KATEGORI as $key => $label) {
            $katMap[strtolower($key)]   = $key;
            $katMap[strtolower($label)] = $key;
        }

        $created = 0; $updated = 0; $skipped = 0;
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $row = str_getcsv($line, $delim);
            $get = function ($col) use ($row, $header) {
                $i = array_search($col, $header);
                return $i !== false && isset($row[$i]) ? trim($row[$i]) : null;
            };

            $kategori = $katMap[strtolower((string) $get('kategori'))] ?? null;
            $nama     = $get('nama');
            $lat      = $this->normalisasiKoordinat($get('latitude'));
            $lng      = $this->normalisasiKoordinat($get('longitude'));
            if (!$kategori || !$nama || !is_numeric($lat) || !is_numeric($lng)) { $skipped++; continue; }

            $level = null;
            if ($kategori === 'banjir_rawan') {
                $lv = (int) $get('level');
                $level = ($lv >= 1 && $lv <= 3) ? $lv : null;
            }
            $link = ($kategori !== 'banjir_rawan') ? ($get('link_maps') ?: null) : null;
            $kecName = strtolower((string) $get('kecamatan'));

            $rec = TitikBencana::updateOrCreate(
                ['kategori' => $kategori, 'nama' => $nama],
                [
                    'level'        => $level,
                    'kecamatan_id' => isset($kecMap[$kecName]) ? $kecMap[$kecName]->id : null,
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'link_maps'    => $link,
                    'keterangan'   => $get('keterangan') ?: null,
                ]
            );
            $rec->wasRecentlyCreated ? $created++ : $updated++;
        }

        return back()->with('success', "Import selesai — $created ditambah, $updated diperbarui, $skipped baris dilewati (kategori/nama/koordinat tidak valid).");
    }

    private function validated(Request $request): array
    {
        // Normalisasi input sebelum validasi:
        // - link Maps tanpa skema (mis. "maps.app.goo.gl/..") → tambahkan https://
        // - koordinat pakai koma desimal ("-6,17") → titik
        if ($request->filled('link_maps') && !preg_match('~^https?://~i', trim($request->input('link_maps')))) {
            $request->merge(['link_maps' => 'https://' . ltrim(trim($request->input('link_maps')), '/')]);
        }
        foreach (['latitude', 'longitude'] as $koord) {
            $val = $request->input($koord);
            if (is_string($val) && str_contains($val, ',') && !str_contains($val, '.')) {
                $request->merge([$koord => str_replace(',', '.', $val)]);
            }
        }

        $data = $request->validate([
            'kecamatan_id' => ['nullable', 'exists:kecamatan,id'],
            'kategori'     => ['required', Rule::in(array_keys(TitikBencana::KATEGORI))],
            'level'        => ['nullable', 'integer', 'between:1,3'],
            'nama'         => ['required', 'string', 'max:255'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'link_maps'    => ['nullable', 'url', 'max:1000'],
            'keterangan'   => ['nullable', 'string'],
        ]);

        // Kolom opsional yang kosong → null (kolom integer tidak menerima string kosong)
        $data['kecamatan_id'] = ($data['kecamatan_id'] ?? '') !== '' ? $data['kecamatan_id'] : null;
        $data['level']        = ($data['level'] ?? '') !== '' ? (int) $data['level'] : null;

        if ($data['kategori'] === 'banjir_rawan') {
            // Banjir tidak butuh link Maps (hanya info lokasi rawan)
            $data['link_maps'] = null;
        } else {
            // Level hanya relevan untuk zona rawan banjir
            $data['level'] = null;
        }

        return $data;
    }
}
