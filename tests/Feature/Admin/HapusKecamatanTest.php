<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\PendudukKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Foreign key ke kecamatan memakai cascadeOnDelete: satu klik hapus bisa
 * melenyapkan data 9 modul untuk semua tahun tanpa bisa dibatalkan.
 */
class HapusKecamatanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_kecamatan_yang_masih_punya_data_tidak_bisa_dihapus(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 100]);

        $this->actingAs($this->admin())
            ->delete(route('admin.kecamatan.destroy', $k))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('kecamatan', ['id' => $k->id]);
        $this->assertDatabaseCount('penduduk_kecamatan', 1);
    }

    public function test_kecamatan_kosong_tetap_bisa_dihapus(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->delete(route('admin.kecamatan.destroy', $k))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('kecamatan', ['id' => $k->id]);
    }

    public function test_halaman_kecamatan_menampilkan_jumlah_data_terkait(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 100]);
        PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2024, 'jumlah_penduduk' => 90]);

        $this->actingAs($this->admin())
            ->get(route('admin.kecamatan.index'))
            ->assertOk()
            ->assertSee('2 baris di 1 modul');
    }

    public function test_nama_kecamatan_tidak_boleh_kembar(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->post(route('admin.kecamatan.store'), ['nama_kecamatan' => 'Cakung'])
            ->assertSessionHasErrors('nama_kecamatan');

        $this->assertDatabaseCount('kecamatan', 1);
    }
}
