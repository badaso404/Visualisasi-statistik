<?php

namespace Tests\Feature\Admin;

use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Export/import CSV tabel anak data geografis.
 *
 * Yang khas di sini: periode baris ada di record INDUK, jadi berkasnya memakai
 * kolom 'kecamatan' + 'tahun' yang terbaca manusia sementara database memakai
 * kecamatan_id + data_geografis_id.
 */
class LuasKecamatanTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = 'kecamatan,tahun,luas_km2,persentase,jumlah_kelurahan,jumlah_rw,jumlah_rt';

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function geografis(int $tahun = 2024): DataGeografis
    {
        return DataGeografis::create([
            'tahun'           => $tahun,
            'luas_kota_km2'   => 129.54,
            'ketinggian_mdpl' => 7,
            'sumber'          => 'BPS',
        ]);
    }

    private function unggah(string $isi)
    {
        return $this->actingAs($this->admin())->post(route('admin.luas-kecamatan.import'), [
            'file' => UploadedFile::fake()->createWithContent('luas.csv', $isi),
        ]);
    }

    public function test_halaman_geografis_menampilkan_alat_csv(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.geografis.index'))
            ->assertOk()
            ->assertSee('Luas per Kecamatan')
            ->assertSee('Export CSV');
    }

    public function test_template_memuat_seluruh_kolom(): void
    {
        $isi = $this->actingAs($this->admin())
            ->get(route('admin.luas-kecamatan.template'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString(self::HEADER, $isi);
    }

    /** Export menulis nama kecamatan & tahun, bukan id yang tak berarti bagi operator. */
    public function test_export_memakai_nama_kecamatan_dan_tahun(): void
    {
        $geo = $this->geografis(2024);
        $kec = Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        LuasKecamatan::create([
            'kecamatan_id' => $kec->id, 'data_geografis_id' => $geo->id,
            'luas_km2' => 24.16, 'persentase' => 18.75,
            'jumlah_kelurahan' => 6, 'jumlah_rw' => 54, 'jumlah_rt' => 552,
        ]);

        $isi = $this->actingAs($this->admin())
            ->get(route('admin.luas-kecamatan.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString(self::HEADER, $isi);
        $this->assertStringContainsString('Kembangan,2024,24.16,18.75,6,54,552', $isi);
    }

    public function test_import_membuat_baris_baru(): void
    {
        $this->geografis(2024);
        Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        $this->unggah(self::HEADER . "\nKembangan,2024,24.16,18.75,6,54,552\n")
            ->assertRedirect(route('admin.geografis.index'));

        $baris = LuasKecamatan::first();
        $this->assertSame('24.16', $baris->luas_km2);
        $this->assertSame(6, $baris->jumlah_kelurahan);
    }

    public function test_import_memperbarui_tanpa_menduplikat(): void
    {
        $geo = $this->geografis(2024);
        $kec = Kecamatan::create(['nama_kecamatan' => 'Kembangan']);
        LuasKecamatan::create([
            'kecamatan_id' => $kec->id, 'data_geografis_id' => $geo->id,
            'luas_km2' => 1, 'persentase' => 1,
        ]);

        $this->unggah(self::HEADER . "\nKembangan,2024,24.16,18.75,6,54,552\n")
            ->assertRedirect(route('admin.geografis.index'));

        $this->assertSame(1, LuasKecamatan::count());
        $this->assertSame('24.16', LuasKecamatan::first()->luas_km2);
    }

    /** Kecamatan sama di dua tahun geografis adalah dua baris berbeda. */
    public function test_kecamatan_sama_pada_tahun_berbeda_terpisah(): void
    {
        $this->geografis(2024);
        $this->geografis(2023);
        Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        $this->unggah(
            self::HEADER . "\n"
            . "Kembangan,2024,24.16,18.75,6,54,552\n"
            . "Kembangan,2023,24.16,18.70,6,53,548\n"
        )->assertRedirect(route('admin.geografis.index'));

        $this->assertSame(2, LuasKecamatan::count());
    }

    public function test_nama_kecamatan_tidak_peka_huruf_besar_kecil(): void
    {
        $this->geografis(2024);
        Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        $this->unggah(self::HEADER . "\n  kEMBANGAN  ,2024,24.16,18.75,,,\n")
            ->assertRedirect(route('admin.geografis.index'));

        $this->assertSame(1, LuasKecamatan::count());
    }

    public function test_kecamatan_tak_dikenal_dilewati_dengan_pesan(): void
    {
        $this->geografis(2024);

        $this->unggah(self::HEADER . "\nBogor,2024,24.16,18.75,,,\n")
            ->assertSessionHas('error');

        $this->assertSame(0, LuasKecamatan::count());
    }

    /**
     * Tahun tanpa record geografis induk tidak boleh membuat induk diam-diam:
     * data_geografis punya kolom wajib sendiri yang tak ada di berkas ini.
     */
    public function test_tahun_tanpa_induk_ditolak_dengan_pesan_yang_bisa_ditindaklanjuti(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        $this->unggah(self::HEADER . "\nKembangan,2099,24.16,18.75,,,\n");

        $this->assertSame(0, LuasKecamatan::count());
        $this->assertSame(0, DataGeografis::count());
        $this->assertStringContainsString('belum ada data geografis untuk tahun 2099', session('error'));
    }

    /** Kelurahan/RW/RT boleh kosong; luas & persentase tidak. */
    public function test_baris_baru_tanpa_luas_dilewati(): void
    {
        $this->geografis(2024);
        Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        $this->unggah(self::HEADER . "\nKembangan,2024,,,6,54,552\n")
            ->assertRedirect(route('admin.geografis.index'));

        $this->assertSame(0, LuasKecamatan::count());
    }

    public function test_header_tanpa_kolom_kunci_ditolak(): void
    {
        $this->unggah("luas_km2,persentase\n24.16,18.75\n")->assertSessionHas('error');

        $this->assertSame(0, LuasKecamatan::count());
    }

    /** Kolom BPS ini sebelumnya hanya bisa diisi seeder, kini lewat modal juga. */
    public function test_form_modal_bisa_menyimpan_kelurahan_rw_rt(): void
    {
        $geo = $this->geografis(2024);
        $kec = Kecamatan::create(['nama_kecamatan' => 'Kembangan']);

        $this->actingAs($this->admin())
            ->post(route('admin.luas-kecamatan.store'), [
                'kecamatan_id' => $kec->id, 'data_geografis_id' => $geo->id,
                'luas_km2' => 24.16, 'persentase' => 18.75,
                'jumlah_kelurahan' => 6, 'jumlah_rw' => 54, 'jumlah_rt' => 552,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(54, LuasKecamatan::first()->jumlah_rw);
    }

    public function test_tamu_tidak_bisa_mengekspor(): void
    {
        $this->get(route('admin.luas-kecamatan.export'))->assertRedirect(route('admin.login'));
    }
}
