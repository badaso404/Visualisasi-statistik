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
            // 'titik bencana' digabung jadi tab di halaman 'bencana' (lihat tes di bawah)
            'bencana'               => ['admin.bencana.index'],
            'kemiskinan'            => ['admin.kemiskinan.index'],
            'infrastruktur digital' => ['admin.infrastruktur-digital.index'],
        ];
    }

    /** @dataProvider halamanIndex */
    public function test_halaman_index_merender(string $route): void
    {
        $this->actingAs($this->admin())->get(route($route))->assertOk();
    }

    public function test_halaman_bencana_memuat_tab_titik_peta(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.bencana.index'))
            ->assertOk()
            ->assertSee('Titik Peta Bencana')
            ->assertSee('modalTitikBencana', false);
    }

    public function test_route_titik_bencana_lama_redirect_ke_bencana(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.titik-bencana.index'))
            ->assertRedirect(route('admin.bencana.index'));
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
