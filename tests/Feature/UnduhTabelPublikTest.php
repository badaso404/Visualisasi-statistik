<?php

namespace Tests\Feature;

use App\Models\DataBencana;
use App\Models\DataGeografis;
use App\Models\DataIklim;
use App\Models\DataKemiskinan;
use App\Models\DataKesehatan;
use App\Models\DataPendidikan;
use App\Models\DataPerekonomian;
use App\Models\FasilitasKesehatanKecamatan;
use App\Models\Kecamatan;
use App\Models\KemiskinanKecamatan;
use App\Models\LuasKecamatan;
use App\Models\PdrbSektor;
use App\Models\PendidikanKecamatan;
use App\Models\PendudukKecamatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Setiap tabel di halaman publik harus punya tombol unduh CSV.
 *
 * Isinya disusun di browser dari tabel yang sudah dirender, jadi yang bisa
 * dijamin dari sisi server hanyalah: tombolnya ada, menunjuk ke tabel yang
 * benar-benar ada di halaman, dan tabel itu menyatakan format angkanya.
 */
class UnduhTabelPublikTest extends TestCase
{
    use RefreshDatabase;

    private Kecamatan $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kecamatan = Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);
    }

    private function isiSemuaModul(): void
    {
        $geo = DataGeografis::create([
            'tahun' => 2025, 'luas_kota_km2' => 129.54, 'ketinggian_mdpl' => 7,
        ]);
        LuasKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'data_geografis_id' => $geo->id,
            'luas_km2' => 17.53, 'persentase' => 13.5,
        ]);
        PendudukKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'tahun' => 2025, 'jumlah_penduduk' => 350000,
        ]);

        DataIklim::create([
            'tahun' => 2025, 'bulan' => 1, 'hari_hujan' => 18, 'tekanan_udara' => 1009.2,
            'suhu_udara' => 27.6, 'kecepatan_angin' => 2.4,
            'kelembaban_udara' => 81, 'penyinaran_matahari' => 45,
        ]);

        DataPendidikan::create([
            'tahun' => 2025, 'apm_sd_mi' => 99, 'apm_smp_mts' => 88, 'apm_sma_smk_man' => 77,
            'apk_sd_mi' => 105, 'apk_smp_mts' => 95, 'apk_sma_smk_man' => 85,
        ]);
        PendidikanKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'tahun' => 2025,
            'jumlah_pelajar' => 12000, 'jumlah_pendidik' => 800,
        ]);

        DataKesehatan::create(['tahun' => 2025, 'jumlah_tempat_tidur_rs' => 1200]);
        FasilitasKesehatanKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'tahun' => 2025,
            'jumlah_total' => 90, 'posyandu' => 60, 'klinik_kesehatan' => 20,
            'puskesmas' => 8, 'rumah_sakit' => 2,
        ]);

        DataBencana::create([
            'tahun' => 2025, 'periode_data' => '202503', 'triwulan' => 1,
            'wilayah' => DataBencana::WILAYAH_JAKBAR, 'jenis_bencana' => 'Banjir',
            'jumlah_kejadian' => 12, 'jumlah_korban_meninggal' => 0, 'jumlah_korban_luka' => 3,
        ]);

        DataKemiskinan::create([
            'tahun' => 2025, 'jumlah_penduduk_miskin' => 100000,
            'persentase_penduduk_miskin' => 4.1, 'garis_kemiskinan' => 700000,
            'indeks_kedalaman' => 0.5, 'indeks_keparahan' => 0.1,
        ]);
        KemiskinanKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'tahun' => 2025,
            'jumlah_penduduk_miskin' => 12000, 'jumlah_keluarga_miskin' => 3000,
            'penerima_bantuan' => 2500, 'persentase' => 3.4,
        ]);

        DataPerekonomian::create([
            'tahun' => 2025, 'pdrb_adhb' => 627869621.19,
            'pdrb_adhk' => 383113079.03, 'laju_pertumbuhan' => 5.27,
        ]);
        PdrbSektor::create([
            'tahun' => 2025, 'kode_sektor' => 7, 'kategori' => 'G',
            'nama_sektor' => 'Perdagangan', 'adhb' => 123886000,
            'distribusi' => 19.73, 'laju_pertumbuhan' => 6.63,
        ]);
    }

    /**
     * [route => id tabel yang jadi sasaran tombol]. Kependudukan tidak masuk
     * daftar karena halamannya memang tidak punya tabel HTML — seluruh datanya
     * disajikan sebagai grafik.
     */
    public static function tabelPublik(): array
    {
        return [
            'overview'     => ['statistik.overview', 'tabel-overview-kecamatan'],
            'geografis'    => ['statistik.geografis', 'geo-table'],
            'iklim'        => ['statistik.iklim', 'iklim-table'],
            'pendidikan'   => ['statistik.pendidikan', 'tabel-pendidikan-kecamatan'],
            'kesehatan'    => ['statistik.kesehatan', 'tabel-faskes-kecamatan'],
            'bencana'      => ['statistik.bencana', 'tabel-bencana-rekap'],
            // Tabel per-kecamatan kemiskinan sengaja dinonaktifkan di view (BPS
            // hanya merilis sampai level kota), jadi yang aktif ringkasan tahun.
            'kemiskinan'   => ['statistik.kemiskinan', 'tabel-kemiskinan-tahun'],
            'perekonomian' => ['statistik.perekonomian', 'tabel-lapangan-usaha'],
        ];
    }

    /** @dataProvider tabelPublik */
    public function test_tabel_punya_tombol_unduh(string $route, string $idTabel): void
    {
        $this->isiSemuaModul();

        $this->get(route($route))
            ->assertOk()
            ->assertSee('Unduh CSV')
            ->assertSee('data-unduh-tabel="#' . $idTabel . '"', false)
            ->assertSee('id="' . $idTabel . '"', false);
    }

    /** Tabel yang jadi sasaran wajib menyatakan format angkanya. */
    public function test_setiap_tabel_sasaran_menyatakan_format_angka(): void
    {
        $this->isiSemuaModul();

        foreach (self::tabelPublik() as [$route, $idTabel]) {
            $html = $this->get(route($route))->assertOk()->getContent();

            $this->assertMatchesRegularExpression(
                '/id="' . preg_quote($idTabel, '/') . '"[^>]*data-unduh-angka="(id|en)"/',
                $html,
                "Tabel {$idTabel} pada {$route} belum menyatakan data-unduh-angka.",
            );
        }
    }

    /** Modul yang punya dua tabel harus bisa mengunduh keduanya terpisah. */
    public function test_modul_dua_tabel_punya_dua_tombol(): void
    {
        $this->isiSemuaModul();

        $this->get(route('statistik.perekonomian'))
            ->assertSee('data-unduh-tabel="#tabel-lapangan-usaha"', false)
            ->assertSee('data-unduh-tabel="#tabel-perekonomian-tahun"', false);
    }

    /** Skrip pengunduh cukup dimuat sekali walau ada beberapa tombol. */
    public function test_skrip_pengunduh_hanya_dimuat_sekali(): void
    {
        $this->isiSemuaModul();

        $html = $this->get(route('statistik.kemiskinan'))->getContent();

        $this->assertSame(1, substr_count($html, 'data-unduh-tabel]'));
    }

    /** Nama berkas membawa tahun agar unduhan lintas tahun tidak saling menimpa. */
    public function test_nama_berkas_menyertakan_tahun_terpilih(): void
    {
        $this->isiSemuaModul();

        $this->get(route('statistik.pendidikan', ['tahun' => 2025]))
            ->assertSee('data-unduh-nama="pendidikan-per-kecamatan-2025"', false);
    }
}
