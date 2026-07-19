<?php

namespace Tests\Feature;

use App\Models\DataGeografis;
use App\Models\DataKependudukan;
use App\Models\DataPendidikan;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;
use App\Models\PendidikanKecamatan;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pasangan HalamanPublikTest: memastikan jalur "ada data" tetap utuh setelah
 * penambahan early-return dataKosong(), bukan cuma jalur kosongnya.
 */
class HalamanPublikBerisiTest extends TestCase
{
    use RefreshDatabase;

    private Kecamatan $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kecamatan = Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);
    }

    public function test_geografis_menampilkan_data(): void
    {
        $geo = DataGeografis::create([
            'tahun' => 2025, 'luas_kota_km2' => 129.54, 'ketinggian_mdpl' => 7,
        ]);
        LuasKecamatan::create([
            'kecamatan_id'      => $this->kecamatan->id,
            'data_geografis_id' => $geo->id,
            'luas_km2'          => 17.53,
            'persentase'        => 13.5,
        ]);
        PendudukKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'tahun' => 2025, 'jumlah_penduduk' => 350000,
        ]);

        $this->get(route('statistik.geografis'))
            ->assertOk()
            ->assertSee('Kebon Jeruk')
            ->assertDontSee('Data belum tersedia');
    }

    public function test_kependudukan_menampilkan_data(): void
    {
        DataKependudukan::create([
            'tahun' => 2025, 'jumlah_laki_laki' => 100, 'jumlah_perempuan' => 120, 'jumlah_total' => 220,
        ]);
        PendudukKecamatan::create([
            'kecamatan_id' => $this->kecamatan->id, 'tahun' => 2025, 'jumlah_penduduk' => 220,
        ]);
        PendudukKelurahan::create([
            'kecamatan_id'   => $this->kecamatan->id, 'tahun' => 2025,
            'nama_kelurahan' => 'Kelapa Dua', 'jumlah_penduduk' => 220,
        ]);

        $this->get(route('statistik.kependudukan'))
            ->assertOk()
            ->assertSee('Kebon Jeruk')
            ->assertDontSee('Data belum tersedia');
    }

    public function test_pendidikan_menampilkan_data(): void
    {
        DataPendidikan::create([
            'tahun' => 2025,
            'apm_sd_mi' => 98.5, 'apm_smp_mts' => 90.1, 'apm_sma_smk_man' => 80.2,
            'apk_sd_mi' => 105.3, 'apk_smp_mts' => 95.4, 'apk_sma_smk_man' => 85.5,
        ]);
        PendidikanKecamatan::create([
            'kecamatan_id'          => $this->kecamatan->id, 'tahun' => 2025,
            'jumlah_pelajar'        => 5000, 'jumlah_pendidik' => 300,
            'jumlah_sekolah_negeri' => 20, 'jumlah_sekolah_swasta' => 15,
        ]);

        $this->get(route('statistik.pendidikan'))
            ->assertOk()
            ->assertSee('Kebon Jeruk')
            ->assertDontSee('Data belum tersedia');
    }

    /** Tahun yang ada datanya tetap dipilih walau pengunjung meminta tahun lain. */
    public function test_meminta_tahun_kosong_tidak_menghapus_akses_ke_tahun_berisi(): void
    {
        DataKependudukan::create([
            'tahun' => 2025, 'jumlah_laki_laki' => 100, 'jumlah_perempuan' => 120, 'jumlah_total' => 220,
        ]);

        // Tahun tak dikenal -> controller jatuh ke tahun terbaru yang tersedia.
        $this->get(route('statistik.kependudukan', ['tahun' => 1999]))
            ->assertOk()
            ->assertDontSee('Data belum tersedia');
    }
}
