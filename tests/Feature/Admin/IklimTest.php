<?php

namespace Tests\Feature\Admin;

use App\Models\DataIklim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Export/import CSV data iklim bulanan — jalur pengisian utama modul ini,
 * karena satu tahun berarti 12 baris x 6 besaran.
 */
class IklimTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function unggah(string $isi)
    {
        return $this->actingAs($this->admin())->post(route('admin.iklim.import'), [
            'file' => UploadedFile::fake()->createWithContent('iklim.csv', $isi),
        ]);
    }

    private function baris(int $tahun, int $bulan, array $ganti = []): DataIklim
    {
        return DataIklim::create($ganti + [
            'tahun' => $tahun, 'bulan' => $bulan,
            'hari_hujan' => 18, 'tekanan_udara' => 1009.20, 'suhu_udara' => 27.60,
            'kecepatan_angin' => 2.40, 'kelembaban_udara' => 81, 'penyinaran_matahari' => 45,
        ]);
    }

    private const HEADER = 'tahun,bulan,hari_hujan,tekanan_udara,suhu_udara,'
        . 'kecepatan_angin,kelembaban_udara,penyinaran_matahari,sumber';

    public function test_halaman_index_menampilkan_alat_csv(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.iklim.index'))
            ->assertOk()
            ->assertSee('Export CSV')
            ->assertSee('Import CSV');
    }

    public function test_template_memuat_seluruh_kolom(): void
    {
        $isi = $this->actingAs($this->admin())
            ->get(route('admin.iklim.template'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString(self::HEADER, $isi);
    }

    public function test_export_memuat_baris_yang_ada(): void
    {
        $this->baris(2024, 3, ['sumber' => 'BMKG']);

        $isi = $this->actingAs($this->admin())
            ->get(route('admin.iklim.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString(self::HEADER, $isi);
        $this->assertStringContainsString('2024,3,18.00,1009.20,27.60', $isi);
    }

    /** Satu berkas berisi setahun penuh — ini alasan utama fitur ini ada. */
    public function test_import_setahun_penuh_sekaligus(): void
    {
        $baris = '';
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $baris .= "2024,{$bulan},18,1009.2,27.6,2.4,81,45,BMKG\n";
        }

        $this->unggah(self::HEADER . "\n" . $baris)
            ->assertRedirect(route('admin.iklim.index'));

        $this->assertSame(12, DataIklim::where('tahun', 2024)->count());
    }

    public function test_import_memperbarui_baris_yang_sudah_ada_tanpa_menduplikat(): void
    {
        $this->baris(2024, 1, ['suhu_udara' => 20]);

        $this->unggah(self::HEADER . "\n2024,1,18,1009.2,28.9,2.4,81,45,BMKG\n")
            ->assertRedirect(route('admin.iklim.index'));

        $this->assertSame(1, DataIklim::count());
        $this->assertSame('28.90', DataIklim::where('tahun', 2024)->where('bulan', 1)->value('suhu_udara'));
    }

    /** Bulan sama di tahun berbeda adalah baris berbeda, bukan duplikat. */
    public function test_bulan_sama_pada_tahun_berbeda_tetap_terpisah(): void
    {
        $this->unggah(
            self::HEADER . "\n"
            . "2024,1,18,1009.2,27.6,2.4,81,45,BMKG\n"
            . "2023,1,20,1010.0,27.1,2.2,83,42,BMKG\n"
        )->assertRedirect(route('admin.iklim.index'));

        $this->assertSame(2, DataIklim::where('bulan', 1)->count());
    }

    /**
     * Bulan di luar 1-12 harus dilewati dengan pesan. Tanpa pemeriksaan ini
     * nilainya baru ditolak database di tengah transaksi, dan seluruh berkas
     * ikut dibatalkan padahal hanya satu baris yang salah.
     */
    public function test_bulan_di_luar_jangkauan_dilewati_tanpa_membatalkan_berkas(): void
    {
        $this->unggah(
            self::HEADER . "\n"
            . "2024,13,18,1009.2,27.6,2.4,81,45,BMKG\n"
            . "2024,2,18,1009.2,27.6,2.4,81,45,BMKG\n"
        )->assertRedirect(route('admin.iklim.index'));

        $this->assertSame(1, DataIklim::count());
        $this->assertSame(2, DataIklim::first()->bulan);
    }

    /** Excel lokal menulis desimal berkoma dan pemisah titik-koma. */
    public function test_import_menerima_format_excel_lokal(): void
    {
        $this->unggah(
            str_replace(',', ';', self::HEADER) . "\n"
            . "2024;1;18;1009,2;27,6;2,4;81;45;BMKG\n"
        )->assertRedirect(route('admin.iklim.index'));

        $this->assertSame('1009.20', DataIklim::first()->tekanan_udara);
    }

    /** Baris baru yang kolom wajibnya belum lengkap dilewati, bukan error 500. */
    public function test_baris_baru_tak_lengkap_dilewati(): void
    {
        $this->unggah(self::HEADER . "\n2024,1,18,,,,,,\n")
            ->assertRedirect(route('admin.iklim.index'));

        $this->assertSame(0, DataIklim::count());
    }

    public function test_header_tanpa_kolom_kunci_ditolak(): void
    {
        $this->unggah("suhu_udara,hari_hujan\n27.6,18\n")->assertSessionHas('error');

        $this->assertSame(0, DataIklim::count());
    }

    public function test_tamu_tidak_bisa_mengekspor(): void
    {
        $this->get(route('admin.iklim.export'))->assertRedirect(route('admin.login'));
    }
}
