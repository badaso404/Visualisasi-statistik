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
use App\Models\DataPerekonomian;
use App\Models\PdrbSektor;
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

    public function test_perekonomian_menampilkan_data(): void
    {
        DataPerekonomian::create([
            'tahun'     => 2024,
            'pdrb_adhb' => 627869621.19, 'pdrb_adhk' => 383113079.03,
            'laju_pertumbuhan' => 5.27, 'sumber' => 'BPS Kota Jakarta Barat',
        ]);
        PdrbSektor::create([
            'tahun' => 2024, 'kode_sektor' => 7, 'kategori' => 'G',
            'nama_sektor' => 'Perdagangan Besar dan Eceran',
            'adhb' => 123886000,
            'distribusi' => 19.73, 'laju_pertumbuhan' => 6.63,
        ]);

        $this->get(route('statistik.perekonomian'))
            ->assertOk()
            ->assertSee('Perdagangan Besar dan Eceran')
            // PDRB disimpan dalam juta rupiah, ditampilkan dalam triliun.
            ->assertSee('Rp 627,87 T')
            ->assertDontSee('Data belum tersedia');
    }

    /** Sektor yang menyusut ditandai negatif, bukan dibuang atau dibalik tandanya. */
    public function test_perekonomian_menampilkan_pertumbuhan_negatif(): void
    {
        DataPerekonomian::create([
            'tahun'     => 2020,
            'pdrb_adhb' => 469053301.48, 'pdrb_adhk' => 316172607.15,
            'laju_pertumbuhan' => -0.86,
        ]);
        PdrbSektor::create([
            'tahun' => 2020, 'kode_sektor' => 4, 'kategori' => 'D',
            'nama_sektor' => 'Pengadaan Listrik dan Gas',
            'adhb' => 770000,
            'distribusi' => 0.12, 'laju_pertumbuhan' => -16.78,
        ]);

        $this->get(route('statistik.perekonomian'))
            ->assertOk()
            ->assertSee('-0,86%')
            ->assertSee('-16,78%');
    }

    /**
     * Tabel sektor hanya memuat 7 teratas + satu baris agregat, tetapi totalnya
     * harus tetap sama dengan PDRB — inti dari peringkasan ini.
     */
    public function test_perekonomian_baris_lainnya_menjaga_total(): void
    {
        // 17 sektor: distribusi 10% untuk sektor 1–9, lalu 1,25% untuk sektor 10–17.
        $adhbTotal = 0;
        for ($i = 1; $i <= 17; $i++) {
            $distribusi = $i <= 9 ? 10.0 : 1.25;
            $adhb       = $distribusi * 1000;
            $adhbTotal += $adhb;

            PdrbSektor::create([
                'tahun' => 2024, 'kode_sektor' => $i, 'kategori' => 'X',
                'nama_sektor' => "Sektor {$i}", 'adhb' => $adhb,
                'distribusi' => $distribusi, 'laju_pertumbuhan' => 5,
            ]);
        }

        DataPerekonomian::create([
            'tahun' => 2024, 'pdrb_adhb' => $adhbTotal, 'pdrb_adhk' => $adhbTotal,
            'laju_pertumbuhan' => 5,
        ]);

        $html = $this->get(route('statistik.perekonomian'))->assertOk()->getContent();

        // 17 sektor − 7 yang tampil = 10 sektor tergabung.
        $this->assertStringContainsString('Lainnya (10 lapangan usaha)', $html);

        // Sisa distribusi: 2 sektor × 10% + 8 sektor × 1,25% = 30,00%.
        $this->assertStringContainsString('30,00%', $html);

        // Sektor terkecil hilang dari TABEL, tapi tetap ada di grafik pertumbuhan
        // yang memang masih merinci seluruh 17 lapangan usaha.
        $this->assertStringNotContainsString('<td>Sektor 17</td>', $html);
        $this->assertStringContainsString('"Sektor 17"', $html);
    }

    /** Selektor tahun & tabel dipangkas ke 3 tahun terakhir, grafiknya tetap penuh. */
    public function test_perekonomian_tabel_ringkasan_hanya_tahun_terakhir(): void
    {
        foreach ([2019, 2020, 2021, 2022, 2023, 2024] as $t) {
            DataPerekonomian::create([
                'tahun' => $t, 'pdrb_adhb' => 100, 'pdrb_adhk' => 90, 'laju_pertumbuhan' => 5,
            ]);
        }

        $html = $this->get(route('statistik.perekonomian'))->assertOk()->getContent();

        // Grafik tren tetap memakai rentang penuh …
        $this->assertStringContainsString('"2019","2020","2021","2022","2023","2024"', $html);

        // … sementara tabel hanya menyisakan tiga tahun terakhir.
        $baris = substr_count($html, '<td>2019</td>') + substr_count($html, '<td>2020</td>')
               + substr_count($html, '<td>2021</td>');
        $this->assertSame(0, $baris);
        $this->assertStringContainsString('<td>2022</td>', $html);
        $this->assertStringContainsString('<td>2024</td>', $html);

        // Dropdown tahun ikut dibatasi ke jendela yang sama.
        $this->assertStringNotContainsString('tahun=2021', $html);
        $this->assertStringContainsString('tahun=2022', $html);
    }

    /**
     * Tahun di luar jendela selektor (mis. ditulis manual di URL) harus jatuh ke
     * tahun terbaru, bukan menampilkan halaman yang tidak bisa dinavigasi lagi.
     */
    public function test_perekonomian_tahun_di_luar_jendela_jatuh_ke_terbaru(): void
    {
        foreach ([2019, 2023, 2024] as $t) {
            DataPerekonomian::create([
                'tahun' => $t, 'pdrb_adhb' => 100, 'pdrb_adhk' => 90, 'laju_pertumbuhan' => 5,
            ]);
        }

        $this->get(route('statistik.perekonomian', ['tahun' => 2019]))
            ->assertOk()
            ->assertSee('PEREKONOMIAN JAKARTA BARAT 2024');
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
