<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatRingkasanInduk;
use Tests\TestCase;

class KemiskinanTest extends TestCase
{
    use RefreshDatabase;
    use MembuatRingkasanInduk;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_halaman_index_tampil(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.kemiskinan.index'))
            ->assertOk()
            ->assertSee('Ringkasan Tahunan')
            ->assertSee('Per Kecamatan');
    }

    public function test_tambah_kemiskinan_lewat_modal(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.kemiskinan.store'), [
                'tahun'                      => 2025,
                'jumlah_penduduk_miskin'     => 50000,
                'persentase_penduduk_miskin' => 4.25,
                'garis_kemiskinan'           => 650000,
                'indeks_kedalaman'           => 0.65,
                'indeks_keparahan'           => 0.15,
            ])
            ->assertRedirect(route('admin.kemiskinan.index'));

        $this->assertDatabaseHas('data_kemiskinan', ['tahun' => 2025]);
    }

    /** Kolom persentase bertipe desimal — batch tidak boleh membulatkannya. */
    public function test_batch_menerima_nilai_desimal(): void
    {
        $this->indukKemiskinan(2024, 2025, 2026);

        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->post(route('admin.kemiskinan-kecamatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [$k->id => [
                    'jumlah_penduduk_miskin' => 5000,
                    'jumlah_keluarga_miskin' => 1200,
                    'penerima_bantuan'       => 800,
                    'persentase'             => 4.75,
                ]],
            ])
            ->assertRedirect(route('admin.kemiskinan.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kemiskinan_kecamatan', [
            'kecamatan_id' => $k->id,
            'tahun'        => 2025,
            'persentase'   => 4.75,
        ]);
    }

    public function test_form_batch_tampil(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->get(route('admin.kemiskinan-kecamatan.batch', ['tahun' => 2025]))
            ->assertOk()
            ->assertSee('Cakung')
            ->assertSee('Persentase');
    }
}
