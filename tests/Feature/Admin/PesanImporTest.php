<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\PendidikanKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\MembuatRingkasanInduk;
use Tests\TestCase;

/**
 * Umpan balik setelah impor CSV.
 *
 * Versi sebelumnya melaporkan hasil lewat channel 'success' selama ada SATU saja
 * baris yang masuk, sehingga impor yang sebagian barisnya ditolak tetap tampil
 * sebagai notifikasi hijau bertuliskan "berhasil". Operator membaca warna hijau,
 * menutup notifikasi, lalu bingung kenapa datanya tidak muncul — persis keluhan
 * yang memunculkan tes ini.
 */
class PesanImporTest extends TestCase
{
    use RefreshDatabase;
    use MembuatRingkasanInduk;

    private Kecamatan $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kecamatan = Kecamatan::create(['nama_kecamatan' => 'Kalideres']);
        $this->indukPendidikan(2024);   // 2025 sengaja TIDAK dibuat
    }

    private function unggah(string $isi)
    {
        return $this->actingAs(User::factory()->create())
            ->post(route('admin.pendidikan-kecamatan.import'), [
                'file' => UploadedFile::fake()->createWithContent('data.csv', $isi),
            ]);
    }

    private const HEADER = 'kecamatan,tahun,jumlah_pelajar,jumlah_pendidik,'
        . 'jumlah_sekolah_negeri,jumlah_sekolah_swasta';

    /** Semua baris masuk → hijau, tidak ada peringatan. */
    public function test_impor_bersih_dilaporkan_sebagai_sukses(): void
    {
        $this->unggah(self::HEADER . "\nKalideres,2024,11111,222,33,44\n")
            ->assertSessionHas('success')
            ->assertSessionMissing('error');

        $this->assertSame(1, PendidikanKecamatan::count());
    }

    /**
     * Inti perbaikan: satu baris masuk, satu ditolak. Dulu ini muncul hijau
     * bertuliskan "berhasil"; sekarang harus lewat channel peringatan.
     */
    public function test_sebagian_ditolak_tidak_boleh_tampil_sebagai_sukses(): void
    {
        $this->unggah(
            self::HEADER . "\n"
            . "Kalideres,2024,11111,222,33,44\n"
            . "Kalideres,2025,55555,111,22,33\n"
        )
            ->assertSessionMissing('success')
            ->assertSessionHas('error');

        // Baris yang sah tetap tersimpan — penolakan sebagian bukan pembatalan.
        $this->assertSame(1, PendidikanKecamatan::count());

        $pesan = session('error');
        $this->assertStringContainsString('1 baris tersimpan', $pesan);
        $this->assertStringContainsString('DILEWATI', $pesan);
        // Pesannya harus menyebut sebabnya, bukan sekadar "ada yang gagal".
        $this->assertStringContainsString('tahun 2025', $pesan);
    }

    public function test_semua_ditolak_dilaporkan_sebagai_galat(): void
    {
        $this->unggah(self::HEADER . "\nKalideres,2025,55555,111,22,33\n")
            ->assertSessionHas('error');

        $this->assertSame(0, PendidikanKecamatan::count());
        $this->assertStringContainsString('Tidak ada data yang masuk', session('error'));
    }

    /** Daftar sebab dipangkas agar notifikasi tidak jadi dinding teks. */
    public function test_banyak_kegagalan_diringkas(): void
    {
        $baris = '';
        foreach (range(1, 8) as $i) {
            $baris .= "Kecamatan Palsu {$i},2024,1,1,1,1\n";
        }

        $this->unggah(self::HEADER . "\n" . $baris)->assertSessionHas('error');

        $this->assertStringContainsString('dan 3 baris lain', session('error'));
    }
}
