<?php

namespace Tests\Feature\Admin;

use App\Models\CctvKecamatan;
use App\Models\Kecamatan;
use App\Models\PdrbSektor;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\MembuatRingkasanInduk;
use Tests\TestCase;

/**
 * Tahun tabel anak harus mengikuti tahun yang sudah ada di tabel induk.
 *
 * Alasannya visibilitas, bukan kerapian: StatistikController menyusun daftar
 * tahun halaman publik dari tabel INDUK, jadi baris anak untuk tahun tanpa
 * ringkasan tersimpan tetapi tidak pernah bisa dibuka pengunjung.
 *
 * Ikatan ini ditegakkan validasi (bukan foreign key), sehingga harus berlaku di
 * ketiga pintu masuk: modal, isi massal, dan impor CSV.
 */
class TahunIndukTest extends TestCase
{
    use RefreshDatabase;
    use MembuatRingkasanInduk;

    private Kecamatan $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kecamatan = Kecamatan::create(['nama_kecamatan' => 'Kembangan']);
    }

    private function admin(): User
    {
        return User::factory()->create();
    }

    /* ── Pintu 1: modal tambah/edit ───────────────────────────────────── */

    public function test_modal_menolak_tahun_tanpa_ringkasan_induk(): void
    {
        $this->indukKependudukan(2025);

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.store'), [
                'kecamatan_id' => $this->kecamatan->id,
                'tahun'        => 2026,          // ringkasan 2026 belum ada
                'jumlah_penduduk' => 100,
            ])
            ->assertSessionHasErrors('tahun');

        $this->assertSame(0, PendudukKecamatan::count());
    }

    public function test_modal_menerima_tahun_yang_ada_ringkasannya(): void
    {
        $this->indukKependudukan(2025);

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.store'), [
                'kecamatan_id' => $this->kecamatan->id,
                'tahun'        => 2025,
                'jumlah_penduduk' => 100,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, PendudukKecamatan::count());
    }

    /** Pesannya harus memberi tahu apa yang kurang, bukan sekadar "tidak valid". */
    public function test_pesan_galat_mengarahkan_ke_ringkasan(): void
    {
        $this->indukKependudukan(2025);

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.store'), [
                'kecamatan_id' => $this->kecamatan->id,
                'tahun'        => 2026,
                'jumlah_penduduk' => 100,
            ]);

        $this->assertStringContainsString(
            'ringkasan kependudukan',
            session('errors')->first('tahun'),
        );
    }

    public function test_modal_anak_menampilkan_pilihan_tahun_bukan_ketikan_bebas(): void
    {
        $this->indukKependudukan(2024, 2025);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.kependudukan.index'))->assertOk()->getContent();

        // Dropdown berisi tahun induk...
        $this->assertStringContainsString('<option value="2025"', $html);
        $this->assertStringContainsString('<option value="2024"', $html);
        // ...dan hanya satu input tahun bebas, yaitu milik modal ringkasan induk.
        $this->assertSame(1, substr_count($html, 'type="number" name="tahun"'));
    }

    /* ── Pintu 2: form isi massal ─────────────────────────────────────── */

    public function test_isi_massal_menolak_tahun_tanpa_ringkasan(): void
    {
        $this->indukPendidikan(2025);

        $this->actingAs($this->admin())
            ->post(route('admin.pendidikan-kecamatan.batch.store'), [
                'tahun' => 2026,
                'data'  => [$this->kecamatan->id => ['jumlah_pelajar' => 100]],
            ])
            ->assertSessionHasErrors('tahun');

        $this->assertDatabaseCount('pendidikan_kecamatan', 0);
    }

    /**
     * Form isi massal terbuka pada tahun ringkasan TERBARU, bukan tahun berjalan:
     * tahun berjalan kerap belum punya ringkasan sehingga form langsung terbuka
     * pada pilihan yang tidak bisa disimpan.
     */
    public function test_isi_massal_terbuka_pada_tahun_ringkasan_terbaru(): void
    {
        $this->indukPendidikan(2023, 2024);

        $this->actingAs($this->admin())
            ->get(route('admin.pendidikan-kecamatan.batch'))
            ->assertOk()
            ->assertSee('<option value="2024" selected', false);
    }

    public function test_isi_massal_memperingatkan_bila_ringkasan_belum_ada(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.pendidikan-kecamatan.batch'))
            ->assertOk()
            ->assertSee('Isi ringkasan tahunannya lebih dulu');
    }

    /* ── Pintu 3: impor CSV ───────────────────────────────────────────── */

    public function test_impor_csv_melewati_baris_bertahun_tanpa_ringkasan(): void
    {
        $this->indukKependudukan(2025);

        $csv = "kecamatan,tahun,jumlah_penduduk\n"
             . "Kembangan,2025,350000\n"
             . "Kembangan,2026,360000\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.import'), [
                'file' => UploadedFile::fake()->createWithContent('data.csv', $csv),
            ]);

        // Baris bertahun sah tetap masuk; yang yatim dilewati dengan pesan.
        $this->assertSame(1, PendudukKecamatan::count());
        $this->assertEquals(2025, PendudukKecamatan::first()->tahun);
    }

    public function test_impor_kelurahan_juga_terikat_ringkasan(): void
    {
        $this->indukKependudukan(2025);

        $csv = "kecamatan,tahun,nama_kelurahan,latitude,longitude,jumlah_penduduk\n"
             . "Kembangan,2026,Kelapa Dua,,,52000\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.import'), [
                'file' => UploadedFile::fake()->createWithContent('kel.csv', $csv),
            ]);

        $this->assertSame(0, PendudukKelurahan::count());
    }

    public function test_impor_pdrb_sektor_terikat_ringkasan_pdrb(): void
    {
        $this->indukPerekonomian(2025);

        $csv = "tahun,kode_sektor,kategori,nama_sektor,adhb,distribusi,laju_pertumbuhan\n"
             . "2025,7,G,Perdagangan,123886000,19.73,6.63\n"
             . "2026,7,G,Perdagangan,130000000,19.90,5.10\n";

        $this->actingAs($this->admin())
            ->post(route('admin.pdrb-sektor.import'), [
                'file' => UploadedFile::fake()->createWithContent('sektor.csv', $csv),
            ]);

        $this->assertSame(1, PdrbSektor::count());
        $this->assertEquals(2025, PdrbSektor::first()->tahun);
    }

    /* ── Modul tanpa induk tidak boleh ikut terkunci ──────────────────── */

    /**
     * JakWiFi & CCTV memang tidak punya tabel ringkasan; tahunnya berdiri
     * sendiri. Ikatan induk tidak boleh diterapkan di sana.
     */
    public function test_modul_tanpa_induk_tetap_menerima_tahun_bebas(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cctv.store'), [
                'kecamatan_id' => $this->kecamatan->id,
                'tahun'        => 2026,
                'jumlah_unit'  => 10,
                'unit_aktif'   => 8,
                'terintegrasi' => 5,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, CctvKecamatan::count());
    }

    public function test_isi_massal_tanpa_induk_tetap_pakai_input_bebas(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.cctv.batch'))
            ->assertOk()
            ->assertSee('type="number" name="tahun"', false);
    }
}
