<?php

namespace Tests\Feature;

use App\Models\DataKependudukan;
use App\Models\NgobrolStatistik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman publik tidak boleh error hanya karena datanya belum diisi —
 * kondisi yang gampang terjadi saat modul baru disiapkan atau admin
 * mengosongkan satu tahun dari portal.
 */
class HalamanPublikTest extends TestCase
{
    use RefreshDatabase;

    public static function halaman(): array
    {
        return [
            'overview'              => ['statistik.overview'],
            'geografis'             => ['statistik.geografis'],
            'iklim'                 => ['statistik.iklim'],
            'kependudukan'          => ['statistik.kependudukan'],
            'pendidikan'            => ['statistik.pendidikan'],
            'kesehatan'             => ['statistik.kesehatan'],
            'bencana'               => ['statistik.bencana'],
            'kemiskinan'            => ['statistik.kemiskinan'],
            'perekonomian'          => ['statistik.perekonomian'],
            'infrastruktur digital' => ['statistik.infrastruktur-digital'],
            'fasilitas umum'        => ['statistik.fasilitas-umum'],
            'infografis'            => ['statistik.infografis'],
            'ngobrol statistik'     => ['statistik.ngobrol-statistik'],
        ];
    }

    /** @dataProvider halaman */
    public function test_merender_walau_database_kosong(string $route): void
    {
        $this->get(route($route))->assertOk();
    }

    public function test_modul_tanpa_data_menampilkan_pesan_yang_jelas(): void
    {
        $this->get(route('statistik.kependudukan'))
            ->assertOk()
            ->assertSee('Data belum tersedia');
    }

    public function test_infografis_tampil_di_menu_baru_dan_tidak_di_ngobrol_statistik(): void
    {
        NgobrolStatistik::create([
            'kategori' => 'infografis',
            'judul' => 'Infografis Uji',
            'gambar' => 'ngobrol-statistik/infografis-uji.jpg',
            'tampil' => true,
        ]);

        $this->get(route('statistik.infografis'))
            ->assertOk()
            ->assertSee('Infografis Uji');

        $this->get(route('statistik.ngobrol-statistik'))
            ->assertOk()
            ->assertDontSee('Infografis Uji');
    }

    public function test_rute_infografis_lama_dialihkan_ke_menu_baru(): void
    {
        $this->get(route('statistik.ngobrol-statistik', ['jenis' => 'infografis']))
            ->assertRedirect(route('statistik.infografis'));
    }

    public function test_materi_mempratinjau_pdf_dan_mengunduh_pptx(): void
    {
        NgobrolStatistik::create([
            'kategori' => 'materi',
            'judul' => 'Materi PDF',
            'isi' => 'Isi materi statistik.',
            'presentasi' => 'ngobrol-statistik/materi-uji.pdf',
            'tampil' => true,
        ]);
        NgobrolStatistik::create([
            'kategori' => 'materi',
            'judul' => 'Materi PPT',
            'isi' => 'Isi materi presentasi lama.',
            'presentasi' => 'ngobrol-statistik/presentasi-uji.ppt',
            'tampil' => true,
        ]);
        NgobrolStatistik::create([
            'kategori' => 'materi',
            'judul' => 'Materi PPTX',
            'isi' => 'Isi materi presentasi.',
            'presentasi' => 'ngobrol-statistik/presentasi-uji.pptx',
            'tampil' => true,
        ]);

        $this->get(route('statistik.ngobrol-statistik', ['jenis' => 'materi']))
            ->assertOk()
            ->assertSee('Materi PDF')
            ->assertSee('<iframe', false)
            ->assertSee('storage/ngobrol-statistik/materi-uji.pdf#toolbar=0')
            ->assertSee('Unduh PDF')
            ->assertSee('Materi PPT')
            ->assertSee('storage/ngobrol-statistik/presentasi-uji.ppt" download', false)
            ->assertSee('Materi PPTX')
            ->assertSee('storage/ngobrol-statistik/presentasi-uji.pptx" download', false)
            ->assertSee('Unduh dokumen');
    }

    /** Tahun yang tidak punya data tidak boleh menjatuhkan halaman. */
    public function test_tahun_tanpa_data_tidak_error(): void
    {
        DataKependudukan::create([
            'tahun'            => 2025,
            'jumlah_laki_laki' => 10,
            'jumlah_perempuan' => 10,
            'jumlah_total'     => 20,
        ]);

        $this->get(route('statistik.kependudukan', ['tahun' => 1999]))->assertOk();
    }
}
