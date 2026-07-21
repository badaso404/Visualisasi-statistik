<?php

namespace Tests\Feature;

use App\Models\DataKependudukan;
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
            'geografis'             => ['statistik.geografis'],
            'iklim'                 => ['statistik.iklim'],
            'kependudukan'          => ['statistik.kependudukan'],
            'pendidikan'            => ['statistik.pendidikan'],
            'kesehatan'             => ['statistik.kesehatan'],
            'bencana'               => ['statistik.bencana'],
            'kemiskinan'            => ['statistik.kemiskinan'],
            'perekonomian'          => ['statistik.perekonomian'],
            'infrastruktur digital' => ['statistik.infrastruktur-digital'],
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
