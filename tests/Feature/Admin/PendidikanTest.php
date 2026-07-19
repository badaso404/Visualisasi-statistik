<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\PendidikanKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendidikanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_halaman_index_tampil(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.pendidikan.index'))
            ->assertOk()
            ->assertSee('Ringkasan APM/APK')
            ->assertSee('Per Kecamatan');
    }

    public function test_tambah_pendidikan_lewat_modal(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pendidikan.store'), [
                'tahun'           => 2025,
                'apm_sd_mi'       => 98.5,
                'apm_smp_mts'     => 90.1,
                'apm_sma_smk_man' => 80.2,
                'apk_sd_mi'       => 105.3,
                'apk_smp_mts'     => 95.4,
                'apk_sma_smk_man' => 85.5,
            ])
            ->assertRedirect(route('admin.pendidikan.index'));

        $this->assertDatabaseHas('data_pendidikan', ['tahun' => 2025]);
    }

    public function test_batch_menyimpan_empat_kolom_sekaligus(): void
    {
        $a = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        $b = Kecamatan::create(['nama_kecamatan' => 'Matraman']);

        $this->actingAs($this->admin())
            ->post(route('admin.pendidikan-kecamatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [
                    $a->id => [
                        'jumlah_pelajar'        => 1000,
                        'jumlah_pendidik'       => 80,
                        'jumlah_sekolah_negeri' => 12,
                        'jumlah_sekolah_swasta' => 5,
                    ],
                    // Seluruh kolom kosong -> baris dilewati.
                    $b->id => [
                        'jumlah_pelajar'        => '',
                        'jumlah_pendidik'       => '',
                        'jumlah_sekolah_negeri' => '',
                        'jumlah_sekolah_swasta' => '',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.pendidikan.index'));

        $this->assertDatabaseHas('pendidikan_kecamatan', [
            'kecamatan_id'    => $a->id,
            'tahun'           => 2025,
            'jumlah_pelajar'  => 1000,
            'jumlah_pendidik' => 80,
        ]);
        $this->assertDatabaseMissing('pendidikan_kecamatan', ['kecamatan_id' => $b->id, 'tahun' => 2025]);
    }

    public function test_batch_memperbarui_bukan_menduplikat(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendidikanKecamatan::create([
            'kecamatan_id'          => $k->id,
            'tahun'                 => 2025,
            'jumlah_pelajar'        => 100,
            'jumlah_pendidik'       => 10,
            'jumlah_sekolah_negeri' => 1,
            'jumlah_sekolah_swasta' => 1,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.pendidikan-kecamatan.batch.store'), [
                'tahun' => 2025,
                'data'  => [$k->id => ['jumlah_pelajar' => 999]],
            ]);

        $this->assertDatabaseCount('pendidikan_kecamatan', 1);
        $this->assertDatabaseHas('pendidikan_kecamatan', ['kecamatan_id' => $k->id, 'jumlah_pelajar' => 999]);
    }

    public function test_form_batch_menampilkan_semua_kolom(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $this->actingAs($this->admin())
            ->get(route('admin.pendidikan-kecamatan.batch', ['tahun' => 2025]))
            ->assertOk()
            ->assertSee('Cakung')
            ->assertSee('Pelajar')
            ->assertSee('Sekolah Negeri');
    }
}
