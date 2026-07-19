<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\PendudukKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KependudukanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_halaman_index_tampil(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.kependudukan.index'))
            ->assertOk()
            ->assertSee('Ringkasan Tahunan')
            ->assertSee('Per Kecamatan');
    }

    public function test_tambah_kependudukan_lewat_modal(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.kependudukan.store'), [
                'tahun'            => 2025,
                'jumlah_laki_laki' => 100,
                'jumlah_perempuan' => 120,
                'jumlah_total'     => 220,
                'sumber'           => 'BPS',
            ])
            ->assertRedirect(route('admin.kependudukan.index'));

        $this->assertDatabaseHas('data_kependudukan', ['tahun' => 2025, 'jumlah_total' => 220]);
    }

    public function test_form_batch_menampilkan_semua_kecamatan(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        Kecamatan::create(['nama_kecamatan' => 'Duren Sawit']);

        $this->actingAs($this->admin())
            ->get(route('admin.penduduk-kecamatan.batch', ['tahun' => 2025]))
            ->assertOk()
            ->assertSee('Cakung')
            ->assertSee('Duren Sawit');
    }

    public function test_batch_menyimpan_banyak_kecamatan_sekaligus(): void
    {
        $a = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        $b = Kecamatan::create(['nama_kecamatan' => 'Duren Sawit']);
        $c = Kecamatan::create(['nama_kecamatan' => 'Matraman']);

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [
                    $a->id => ['jumlah_penduduk' => 500],
                    $b->id => ['jumlah_penduduk' => 700],
                    $c->id => ['jumlah_penduduk' => ''], // dikosongkan
                ],
            ])
            ->assertRedirect(route('admin.kependudukan.index'));

        $this->assertDatabaseHas('penduduk_kecamatan', ['kecamatan_id' => $a->id, 'tahun' => 2025, 'jumlah_penduduk' => 500]);
        $this->assertDatabaseHas('penduduk_kecamatan', ['kecamatan_id' => $b->id, 'tahun' => 2025, 'jumlah_penduduk' => 700]);
        // Kolom kosong dilewati, bukan disimpan sebagai 0.
        $this->assertDatabaseMissing('penduduk_kecamatan', ['kecamatan_id' => $c->id, 'tahun' => 2025]);
    }

    public function test_batch_memperbarui_data_yang_sudah_ada_bukan_menduplikat(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 100]);

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [$k->id => ['jumlah_penduduk' => 999]],
            ]);

        $this->assertDatabaseCount('penduduk_kecamatan', 1);
        $this->assertDatabaseHas('penduduk_kecamatan', ['kecamatan_id' => $k->id, 'jumlah_penduduk' => 999]);
    }

    public function test_validasi_gagal_mengembalikan_input(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.kependudukan.store'), ['tahun' => 'bukan-angka'])
            ->assertSessionHasErrors('tahun');
    }
}
