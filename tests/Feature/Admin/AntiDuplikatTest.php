<?php

namespace Tests\Feature\Admin;

use App\Models\DataIklim;
use App\Models\DataKependudukan;
use App\Models\Kecamatan;
use App\Models\PendudukKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatRingkasanInduk;
use Tests\TestCase;

/**
 * Satu periode = satu baris. Kalau aturan ini bocor, isi massal & import CSV
 * hanya memperbarui salah satu baris kembar sementara sisanya tetap terhitung
 * di grafik publik — tanpa pesan error apa pun.
 */
class AntiDuplikatTest extends TestCase
{
    use RefreshDatabase;
    use MembuatRingkasanInduk;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function ringkasan(int $tahun): array
    {
        return [
            'tahun'            => $tahun,
            'jumlah_laki_laki' => 10,
            'jumlah_perempuan' => 10,
            'jumlah_total'     => 20,
        ];
    }

    public function test_ringkasan_tahunan_tidak_boleh_kembar(): void
    {
        $admin = $this->admin();
        DataKependudukan::create($this->ringkasan(2025));

        $this->actingAs($admin)
            ->post(route('admin.kependudukan.store'), $this->ringkasan(2025))
            ->assertSessionHasErrors('tahun');

        $this->assertDatabaseCount('data_kependudukan', 1);
    }

    public function test_per_kecamatan_tidak_boleh_kembar(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        $admin = $this->admin();
        $k     = Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $kirim = ['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 100];

        $this->actingAs($admin)->post(route('admin.penduduk-kecamatan.store'), $kirim)
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.penduduk-kecamatan.store'), $kirim)
            ->assertSessionHasErrors('tahun');

        $this->assertDatabaseCount('penduduk_kecamatan', 1);
    }

    /** Kecamatan berbeda pada tahun sama tetap boleh — kunci gabungan, bukan tahun saja. */
    public function test_kecamatan_berbeda_tahun_sama_tetap_boleh(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        $admin = $this->admin();
        $a     = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        $b     = Kecamatan::create(['nama_kecamatan' => 'Matraman']);

        foreach ([$a, $b] as $k) {
            $this->actingAs($admin)
                ->post(route('admin.penduduk-kecamatan.store'), [
                    'kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 100,
                ])
                ->assertSessionHasNoErrors();
        }

        $this->assertDatabaseCount('penduduk_kecamatan', 2);
    }

    /** Menyimpan baris yang sama tanpa mengubah tahun tidak boleh dianggap bentrok dengan dirinya sendiri. */
    public function test_edit_baris_yang_sama_tidak_dianggap_duplikat(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        $admin = $this->admin();
        $k     = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        $row   = PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 100]);

        $this->actingAs($admin)
            ->put(route('admin.penduduk-kecamatan.update', $row), [
                'kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 555,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penduduk_kecamatan', ['id' => $row->id, 'jumlah_penduduk' => 555]);
    }

    /** Edit yang membuat baris bentrok dengan baris lain harus ditolak. */
    public function test_edit_menjadi_periode_yang_sudah_terpakai_ditolak(): void
    {
        $admin = $this->admin();
        $k     = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2024, 'jumlah_penduduk' => 100]);
        $row = PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 200]);

        $this->actingAs($admin)
            ->put(route('admin.penduduk-kecamatan.update', $row), [
                'kecamatan_id' => $k->id, 'tahun' => 2024, 'jumlah_penduduk' => 200,
            ])
            ->assertSessionHasErrors('tahun');
    }

    public function test_iklim_dikunci_per_bulan_bukan_per_tahun(): void
    {
        $admin = $this->admin();

        $isi = fn (int $bulan) => [
            'tahun' => 2025, 'bulan' => $bulan, 'hari_hujan' => 1, 'tekanan_udara' => 1,
            'suhu_udara' => 28, 'kecepatan_angin' => 1, 'kelembaban_udara' => 70,
            'penyinaran_matahari' => 50,
        ];

        DataIklim::create($isi(1));

        // Bulan lain di tahun sama: boleh.
        $this->actingAs($admin)->post(route('admin.iklim.store'), $isi(2))
            ->assertSessionHasNoErrors();

        // Bulan yang sama: ditolak.
        $this->actingAs($admin)->post(route('admin.iklim.store'), $isi(1))
            ->assertSessionHasErrors('tahun');

        $this->assertDatabaseCount('data_iklim', 2);
    }
}
