<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Asap-tes seluruh halaman admin: setiap index harus merender (termasuk
 * modal-modalnya) dan setiap form isi massal harus bisa dibuka.
 */
class HalamanAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public static function halamanIndex(): array
    {
        return [
            'dashboard'             => ['admin.dashboard'],
            'kecamatan'             => ['admin.kecamatan.index'],
            'geografis'             => ['admin.geografis.index'],
            'iklim'                 => ['admin.iklim.index'],
            'kependudukan'          => ['admin.kependudukan.index'],
            'pendidikan'            => ['admin.pendidikan.index'],
            'kesehatan'             => ['admin.kesehatan.index'],
            'bencana'               => ['admin.bencana.index'],
            'titik bencana'         => ['admin.titik-bencana.index'],
            'kemiskinan'            => ['admin.kemiskinan.index'],
            'infrastruktur digital' => ['admin.infrastruktur-digital.index'],
        ];
    }

    /** @dataProvider halamanIndex */
    public function test_halaman_index_merender(string $route): void
    {
        $this->actingAs($this->admin())->get(route($route))->assertOk();
    }

    public static function halamanBatch(): array
    {
        return [
            'penduduk kecamatan'   => ['admin.penduduk-kecamatan.batch'],
            'pendidikan kecamatan' => ['admin.pendidikan-kecamatan.batch'],
            'tenaga kesehatan'     => ['admin.tenaga-kesehatan.batch'],
            'fasilitas kesehatan'  => ['admin.fasilitas-kesehatan.batch'],
            'kemiskinan kecamatan' => ['admin.kemiskinan-kecamatan.batch'],
            'jak wifi'             => ['admin.jak-wifi.batch'],
            'cctv'                 => ['admin.cctv.batch'],
        ];
    }

    /** @dataProvider halamanBatch */
    public function test_form_isi_massal_merender(string $route): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->get(route($route))
            ->assertOk()
            ->assertSee('Cakung')
            ->assertSee('Simpan Semua');
    }

    public function test_halaman_admin_menolak_tamu(): void
    {
        $this->get(route('admin.kependudukan.index'))->assertRedirect(route('admin.login'));
    }
}
