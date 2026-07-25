<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerPeriode;
use App\Http\Controllers\Admin\Concerns\IsiMassalPerKecamatan;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;
use Illuminate\Http\Request;

/**
 * Rincian luas & pembagian administrasi per kecamatan — tabel anak dari
 * data_geografis.
 *
 * Periodenya tidak disimpan di sini melainkan di record induk, jadi kolom kunci
 * di berkas CSV (kecamatan + tahun) sengaja berbeda dari kunci di database
 * (kecamatan_id + data_geografis_id); penerjemahannya ada di tiga method csv*
 * di bawah.
 */
class LuasKecamatanController extends Controller
{
    use CsvPerPeriode;
    use ValidasiPeriodeUnik;
    use TahunMengikutiInduk;
    use IsiMassalPerKecamatan;

    /* ── Ikatan induk (tahun mengikuti data_geografis) ────────────────── */

    protected function tabelInduk(): ?string
    {
        return 'data_geografis';
    }

    protected function sebutanInduk(): string
    {
        return 'data geografis';
    }

    protected function tabInduk(): string
    {
        return 'tab Data Geografis';
    }

    /* ── Isi massal per kecamatan ─────────────────────────────────────── */

    protected function batchModel(): string
    {
        return LuasKecamatan::class;
    }

    protected function batchFields(): array
    {
        return [
            'luas_km2'         => ['label' => 'Luas (km²)', 'desimal' => true],
            'persentase'       => ['label' => 'Persentase (%)', 'desimal' => true],
            // kelurahan & rw = smallint unsigned (maks 65.535); rt = int unsigned.
            'jumlah_kelurahan' => ['label' => 'Kelurahan', 'maks' => 65535],
            'jumlah_rw'        => ['label' => 'RW', 'maks' => 65535],
            'jumlah_rt'        => ['label' => 'RT', 'maks' => 4294967295],
        ];
    }

    protected function batchJudul(): string
    {
        return 'Isi Massal Luas per Kecamatan';
    }

    protected function batchRoutePrefix(): string
    {
        return 'admin.luas-kecamatan';
    }

    protected function batchRedirect(): string
    {
        return 'admin.geografis.index';
    }

    /*
     * luas_kecamatan menyimpan data_geografis_id (foreign key), bukan kolom
     * `tahun`. Ketiga override ini menerjemahkan tahun yang dipilih operator ke
     * id induk yang sesuai; tahun sudah dijamin ada oleh aturanTahunInduk().
     */

    protected function batchExisting(int $tahun): \Illuminate\Support\Collection
    {
        $id = $this->geografisId($tahun);

        return $id
            ? LuasKecamatan::where('data_geografis_id', $id)->get()->keyBy('kecamatan_id')
            : collect();
    }

    protected function batchTahunAda(): \Illuminate\Support\Collection
    {
        return DataGeografis::whereIn('id', LuasKecamatan::select('data_geografis_id')->distinct())
            ->orderByDesc('tahun')->pluck('tahun');
    }

    protected function batchKunciSimpan(int $tahun, $kecamatanId): array
    {
        return ['kecamatan_id' => $kecamatanId, 'data_geografis_id' => $this->geografisId($tahun)];
    }

    private function geografisId(int $tahun): ?int
    {
        return DataGeografis::where('tahun', $tahun)->value('id');
    }

    /* ── CSV: satu baris per (kecamatan, tahun geografis) ─────────────── */

    protected function csvNama(): string
    {
        return 'luas-kecamatan';
    }

    protected function csvModel(): string
    {
        return LuasKecamatan::class;
    }

    protected function csvKunci(): array
    {
        return ['kecamatan_id', 'data_geografis_id'];
    }

    protected function csvKunciCsv(): array
    {
        return ['kecamatan', 'tahun'];
    }

    protected function csvKolom(): array
    {
        return [
            'luas_km2'         => 'desimal',
            'persentase'       => 'desimal',
            'jumlah_kelurahan' => 'int',
            'jumlah_rw'        => 'int',
            'jumlah_rt'        => 'int',
        ];
    }

    /** Kelurahan/RW/RT nullable (bersumber BPS var 155, tak selalu terisi). */
    protected function csvKolomWajib(): array
    {
        return ['luas_km2', 'persentase'];
    }

    protected function csvContoh(): array
    {
        return [
            'kecamatan'        => Kecamatan::orderBy('nama_kecamatan')->value('nama_kecamatan') ?: 'Kembangan',
            'tahun'            => DataGeografis::max('tahun') ?: now()->year,
            'luas_km2'         => 24.16,
            'persentase'       => 18.75,
            'jumlah_kelurahan' => 6,
            'jumlah_rw'        => 54,
            'jumlah_rt'        => 552,
        ];
    }

    protected function csvRelasiExport(): array
    {
        return ['kecamatan', 'dataGeografis'];
    }

    protected function csvRedirect(): string
    {
        return 'admin.geografis.index';
    }

    /**
     * Nama kecamatan & tahun ditukar menjadi id. Keduanya harus SUDAH ada:
     * record geografis induk punya kolom wajib sendiri (luas kota, jumlah
     * pulau) yang tidak ada di berkas ini, jadi membuatnya diam-diam hanya
     * akan menghasilkan induk setengah jadi.
     */
    protected function csvKunciKeDatabase(array $mentah): array|string
    {
        $nama = trim((string) ($mentah['kecamatan'] ?? ''));
        if ($nama === '') {
            return "kolom 'kecamatan' kosong";
        }

        $kecamatan = $this->kecamatanByNama()->get(mb_strtolower($nama));
        if (!$kecamatan) {
            return "kecamatan '{$nama}' tidak dikenal";
        }

        $tahun = trim((string) ($mentah['tahun'] ?? ''));
        if ($tahun === '' || !is_numeric($tahun)) {
            return "kolom 'tahun' kosong atau bukan angka";
        }

        $geografis = $this->geografisByTahun()->get((int) $tahun);
        if (!$geografis) {
            return "belum ada data geografis untuk tahun {$tahun}; buat dulu record tahunnya";
        }

        return [
            'kecamatan_id'      => $kecamatan->id,
            'data_geografis_id' => $geografis->id,
        ];
    }

    protected function csvKunciDariRecord($record): array
    {
        return [
            $record->kecamatan->nama_kecamatan ?? '',
            $record->dataGeografis->tahun ?? '',
        ];
    }

    /** Kedua peta di-cache per-request; tanpa itu tiap baris CSV memicu query. */
    private function kecamatanByNama()
    {
        return $this->kecamatanByNama ??= Kecamatan::all()
            ->keyBy(fn ($k) => mb_strtolower(trim($k->nama_kecamatan)));
    }

    private function geografisByTahun()
    {
        return $this->geografisByTahun ??= DataGeografis::all()->keyBy('tahun');
    }

    private $kecamatanByNama = null;

    private $geografisByTahun = null;

    public function store(Request $request)
    {
        LuasKecamatan::create($this->validated($request));

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan ditambahkan.');
    }

    public function update(Request $request, LuasKecamatan $luasKecamatan)
    {
        $luasKecamatan->update($this->validated($request, $luasKecamatan));

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan diperbarui.');
    }

    public function destroy(LuasKecamatan $luasKecamatan)
    {
        $luasKecamatan->delete();

        return redirect()->route('admin.geografis.index')->with('success', 'Luas kecamatan dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'kecamatan_id'      => ['required', 'exists:kecamatan,id'],
            'data_geografis_id' => ['required', 'exists:data_geografis,id',
                $this->unikPerPeriode('luas_kecamatan', ['kecamatan_id' => $request->input('kecamatan_id')], $item),
            ],
            'luas_km2'          => ['required', 'numeric', 'min:0'],
            'persentase'        => ['required', 'numeric', 'min:0', 'max:100'],
            // Bersumber BPS var 155; boleh kosong bila tahunnya belum dirilis.
            // Batas ikut tipe kolom: kelurahan & rw = smallint unsigned (65.535),
            // rt = int unsigned — di atas itu MySQL menolak dengan "Out of range".
            'jumlah_kelurahan'  => ['nullable', 'integer', 'min:0', 'max:65535'],
            'jumlah_rw'         => ['nullable', 'integer', 'min:0', 'max:65535'],
            'jumlah_rt'         => ['nullable', 'integer', 'min:0', 'max:4294967295'],
        ], $this->pesanPeriodeUnik('kecamatan ini untuk tahun geografis tersebut') + [
            'jumlah_kelurahan.max' => 'Jumlah kelurahan terlalu besar (maksimum 65.535).',
            'jumlah_rw.max'        => 'Jumlah RW terlalu besar (maksimum 65.535).',
            'jumlah_rt.max'        => 'Jumlah RT terlalu besar (maksimum 4.294.967.295).',
        ]);
    }
}
