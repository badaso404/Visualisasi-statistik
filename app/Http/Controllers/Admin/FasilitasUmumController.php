<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\PesanHasilImpor;
use App\Http\Controllers\Controller;
use App\Models\FasilitasUmum;
use App\Models\Kecamatan;
use App\Services\Statistik\FasilitasJakbarSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CRUD fasilitas umum.
 *
 * Bentuknya beda dari modul admin lain — tidak memakai IsiMassalPerKecamatan
 * maupun CsvPerKecamatan — karena tabelnya juga beda: satu baris = satu
 * fasilitas, bukan satu baris = satu kecamatan per tahun. Isi massal per tahun
 * tidak punya arti di sini, dan CSV-nya butuh kolom teks (nama, alamat) yang
 * tidak dikenal trait bersama itu.
 */
class FasilitasUmumController extends Controller
{
    use PesanHasilImpor;

    /** Baris per halaman. Datanya ratusan, jadi tabelnya wajib berhalaman. */
    private const PER_HALAMAN = 25;

    public function index(Request $request)
    {
        $kategori = $request->get('kategori');
        if (!array_key_exists((string) $kategori, FasilitasUmum::KATEGORI)) {
            $kategori = null;
        }

        $daftar = FasilitasUmum::with('kecamatan')
            ->kategori($kategori)
            ->cari($request->get('q'))
            ->when($request->filled('kecamatan_id'), function ($q) use ($request) {
                // 'kosong' = fasilitas yang belum terpetakan; ini yang biasanya
                // dicari admin sesudah sinkronisasi untuk dirapikan.
                $request->get('kecamatan_id') === 'kosong'
                    ? $q->whereNull('kecamatan_id')
                    : $q->where('kecamatan_id', $request->get('kecamatan_id'));
            })
            ->orderBy('kategori')
            ->orderBy('nama')
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        return view('admin.fasilitas-umum.index', [
            'daftar'          => $daftar,
            'kategori'        => $kategori,
            'kecamatan'       => Kecamatan::orderBy('nama_kecamatan')->get(),
            'jumlahKategori'  => FasilitasUmum::selectRaw('kategori, count(*) c')
                ->groupBy('kategori')->pluck('c', 'kategori'),
            'tanpaKecamatan'  => FasilitasUmum::whereNull('kecamatan_id')->count(),
            'terakhirSinkron' => FasilitasUmum::max('updated_at'),
        ]);
    }

    /**
     * Sinkronisasi SATU kategori sekali jalan.
     *
     * Sengaja tidak menyediakan tombol "tarik semua": sumber membatasi 60
     * permintaan/menit sedangkan seluruh kategori butuh ~78 halaman, jadi satu
     * request web akan menggantung lebih dari semenit dan besar kemungkinan
     * diputus server web sebelum selesai. Untuk penarikan menyeluruh ada
     * `php artisan statistik:sinkron-fasilitas`, yang tidak kena batas itu.
     */
    public function sync(Request $request, FasilitasJakbarSync $sync)
    {
        $data = $request->validate([
            'kategori' => ['required', Rule::in(array_keys(FasilitasUmum::KATEGORI))],
        ]);

        // Tempat ibadah sendirian sudah 61 halaman (~70 detik). Batas waktu PHP
        // dinaikkan supaya prosesnya tidak mati di tengah jalan dan menyisakan
        // sebagian kategori saja.
        set_time_limit(300);

        $hasil = $sync->jalankan([$data['kategori']]);

        return back()->with($hasil['error'] ? 'error' : 'success', $sync->ringkas($hasil));
    }

    public function store(Request $request)
    {
        FasilitasUmum::create($this->validated($request));

        return redirect()->route('admin.fasilitas-umum.index')->with('success', 'Fasilitas ditambahkan.');
    }

    public function update(Request $request, FasilitasUmum $fasilitasUmum)
    {
        $fasilitasUmum->update($this->validated($request));

        return redirect()->route('admin.fasilitas-umum.index')->with('success', 'Fasilitas diperbarui.');
    }

    public function destroy(FasilitasUmum $fasilitasUmum)
    {
        $fasilitasUmum->delete();

        return redirect()->route('admin.fasilitas-umum.index')->with('success', 'Fasilitas dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kategori'     => ['required', Rule::in(array_keys(FasilitasUmum::KATEGORI))],
            'nama'         => ['required', 'string', 'max:255'],
            'alamat'       => ['nullable', 'string', 'max:1000'],
            'kecamatan_id' => ['nullable', 'exists:kecamatan,id'],
            'kelurahan'    => ['nullable', 'string', 'max:255'],
            // Batas koordinat sengaja longgar (bukan kotak Jakarta Barat):
            // beberapa fasilitas transportasi, mis. halte ujung koridor,
            // memang berada tepat di luar batas kota.
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
        ], [], [
            'kecamatan_id' => 'kecamatan',
        ]);
    }

    // ── CSV ──────────────────────────────────────────────────────────

    /** Urutan kolom CSV; dipakai bersama oleh export, template, dan import. */
    private const KOLOM = ['kategori', 'nama', 'alamat', 'kecamatan', 'kelurahan', 'latitude', 'longitude'];

    public function template()
    {
        return $this->streamCsv('template-fasilitas-umum.csv', [[
            'rptra',
            'RPTRA Contoh',
            'Jl. Contoh No.1, Jakarta Barat',
            Kecamatan::orderBy('nama_kecamatan')->value('nama_kecamatan') ?: 'Kalideres',
            'Tegal Alur',
            '-6.1234567',
            '106.7654321',
        ]]);
    }

    public function export()
    {
        $rows = FasilitasUmum::with('kecamatan')
            ->orderBy('kategori')->orderBy('nama')
            ->get()
            ->map(fn (FasilitasUmum $f) => [
                $f->kategori,
                $f->nama,
                $f->alamat,
                $f->kecamatan->nama_kecamatan ?? '',
                $f->kelurahan,
                $f->latitude,
                $f->longitude,
            ])->all();

        return $this->streamCsv('fasilitas-umum-' . now()->format('Ymd-His') . '.csv', $rows);
    }

    /**
     * Impor CSV.
     *
     * Baris dicocokkan pada (kategori + nama). Bukan kunci yang ideal — nama
     * bisa berubah ejaannya — tapi itu satu-satunya penanda yang bisa ditulis
     * manusia di spreadsheet; id sumber tidak terlihat oleh operator dan tidak
     * ada untuk data yang diinput sendiri. Alur yang dimaksudkan: export dulu,
     * sunting di Excel, impor lagi. Terutama untuk melengkapi lat/long, yang
     * tidak dikirim API sumber sama sekali.
     */
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

        $header = array_map(fn ($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $h))), $header);
        $col    = array_flip($header);

        foreach (['kategori', 'nama'] as $wajib) {
            if (!isset($col[$wajib])) {
                fclose($handle);

                return back()->with('error', "Kolom '{$wajib}' tidak ditemukan di header CSV. Unduh template untuk format yang benar.");
            }
        }

        $kecamatanByNama = Kecamatan::all()->keyBy(fn ($k) => strtolower(trim($k->nama_kecamatan)));
        $gagal = [];
        $baris = 1; // baris header

        try {
            $sukses = DB::transaction(function () use ($handle, $pemisah, $col, $kecamatanByNama, &$gagal, &$baris) {
                $jumlah = 0;

                while (($data = fgetcsv($handle, 0, $pemisah)) !== false) {
                    $baris++;
                    if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                        continue;
                    }

                    $ambil = fn ($key) => isset($col[$key], $data[$col[$key]]) ? trim((string) $data[$col[$key]]) : null;

                    $kategori = strtolower((string) $ambil('kategori'));
                    if (!array_key_exists($kategori, FasilitasUmum::KATEGORI)) {
                        $gagal[] = "Baris {$baris}: kategori '" . $ambil('kategori') . "' tidak dikenal";
                        continue;
                    }

                    $nama = (string) $ambil('nama');
                    if ($nama === '') {
                        $gagal[] = "Baris {$baris}: nama kosong";
                        continue;
                    }

                    $nilai = [];

                    // Kolom yang tidak ada di header dilewati; kolom yang ada
                    // tapi dikosongkan DIANGGAP sengaja dikosongkan, supaya
                    // operator bisa menghapus isian keliru lewat CSV.
                    foreach (['alamat', 'kelurahan'] as $teks) {
                        if (isset($col[$teks])) {
                            $nilai[$teks] = $ambil($teks) ?: null;
                        }
                    }

                    foreach (['latitude', 'longitude'] as $koord) {
                        if (!isset($col[$koord])) {
                            continue;
                        }
                        $isi = str_replace(',', '.', (string) $ambil($koord));
                        $nilai[$koord] = is_numeric($isi) ? (float) $isi : null;
                    }

                    if (isset($col['kecamatan'])) {
                        $namaKec = (string) $ambil('kecamatan');
                        if ($namaKec === '') {
                            $nilai['kecamatan_id'] = null;
                        } elseif ($kec = $kecamatanByNama->get(strtolower($namaKec))) {
                            $nilai['kecamatan_id'] = $kec->id;
                        } else {
                            $gagal[] = "Baris {$baris}: kecamatan '{$namaKec}' tidak dikenal";
                            continue;
                        }
                    }

                    FasilitasUmum::updateOrCreate(
                        ['kategori' => $kategori, 'nama' => $nama],
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

        [$channel, $pesan] = $this->hasilImpor($sukses, $gagal);

        return redirect()->route('admin.fasilitas-umum.index')->with($channel, $pesan);
    }

    /** Stream baris ke unduhan CSV; BOM dipasang agar Excel membacanya sebagai UTF-8. */
    private function streamCsv(string $namaBerkas, array $rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::KOLOM);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
