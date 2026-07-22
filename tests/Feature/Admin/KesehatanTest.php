<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatRingkasanInduk;
use Tests\TestCase;

class KesehatanTest extends TestCase
{
    use RefreshDatabase;
    use MembuatRingkasanInduk;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_halaman_index_tampil_dengan_tiga_tab(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.kesehatan.index'))
            ->assertOk()
            ->assertSee('Ringkasan Tahunan')
            ->assertSee('Tenaga Kesehatan')
            ->assertSee('Fasilitas Kesehatan');
    }

    public function test_tambah_kesehatan_lewat_modal(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.kesehatan.store'), [
                'tahun'                  => 2025,
                'jumlah_tempat_tidur_rs' => 1500,
            ])
            ->assertRedirect(route('admin.kesehatan.index'));

        $this->assertDatabaseHas('data_kesehatan', ['tahun' => 2025, 'jumlah_tempat_tidur_rs' => 1500]);
    }

    public function test_batch_tenaga_kesehatan(): void
    {
        $this->indukKesehatan(2024, 2025, 2026);

        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->post(route('admin.tenaga-kesehatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [$k->id => [
                    'jumlah_total' => 200,
                    'dokter'       => 30,
                    'perawat'      => 90,
                    'bidan'        => 40,
                    'ahli_gizi'    => 20,
                    'farmasi'      => 20,
                ]],
            ])
            ->assertRedirect(route('admin.kesehatan.index'));

        $this->assertDatabaseHas('tenaga_kesehatan_kecamatan', [
            'kecamatan_id' => $k->id,
            'tahun'        => 2025,
            'dokter'       => 30,
        ]);
    }

    public function test_batch_fasilitas_kesehatan(): void
    {
        $this->indukKesehatan(2024, 2025, 2026);

        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-kesehatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [$k->id => [
                    'jumlah_total'     => 50,
                    'klinik_kesehatan' => 20,
                    'posyandu'         => 25,
                    'puskesmas'        => 4,
                    'rumah_sakit'      => 1,
                ]],
            ])
            ->assertRedirect(route('admin.kesehatan.index'));

        $this->assertDatabaseHas('fasilitas_kesehatan_kecamatan', [
            'kecamatan_id' => $k->id,
            'puskesmas'    => 4,
        ]);
    }
}
