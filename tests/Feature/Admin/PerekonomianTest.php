<?php

namespace Tests\Feature\Admin;

use App\Models\DataPerekonomian;
use App\Models\PdrbSektor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PerekonomianTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_halaman_index_tampil(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.perekonomian.index'))
            ->assertOk()
            ->assertSee('Ringkasan Tahunan')
            ->assertSee('Lapangan Usaha');
    }

    /**
     * Tab lapangan usaha dikelompokkan per tahun dan hanya tahun terbaru yang
     * terbuka — tanpa itu seluruh tahun tertumpah jadi satu tabel panjang.
     */
    public function test_sektor_dikelompokkan_per_tahun(): void
    {
        foreach ([2023, 2024] as $t) {
            foreach ([1, 2] as $kode) {
                PdrbSektor::create([
                    'tahun' => $t, 'kode_sektor' => $kode, 'kategori' => 'A',
                    'nama_sektor' => "Sektor {$kode}", 'adhb' => 100 * $kode,
                    'distribusi' => 10, 'laju_pertumbuhan' => 5,
                ]);
            }
        }

        $html = $this->actingAs($this->admin())
            ->get(route('admin.perekonomian.index'))->assertOk()->getContent();

        $this->assertStringContainsString('id="sektor-2024"', $html);
        $this->assertStringContainsString('id="sektor-2023"', $html);
        $this->assertStringContainsString('2 lapangan usaha', $html);

        // Tahun terbaru terbuka; tahun lama terlipat.
        $this->assertMatchesRegularExpression('/id="sektor-2024"[^>]*class="[^"]*\bshow\b/', $html);
        $this->assertDoesNotMatchRegularExpression('/id="sektor-2023"[^>]*class="[^"]*\bshow\b/', $html);
    }

    public function test_tambah_perekonomian_lewat_modal(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.perekonomian.store'), [
                'tahun'            => 2024,
                'pdrb_adhb'        => 627869621.19,
                'pdrb_adhk'        => 383113079.03,
                'laju_pertumbuhan' => 5.27,
            ])
            ->assertRedirect(route('admin.perekonomian.index'));

        $this->assertDatabaseHas('data_perekonomian', ['tahun' => 2024]);
    }

    /** Kontraksi ekonomi (mis. 2020) harus bisa disimpan, bukan ditolak validasi. */
    public function test_laju_pertumbuhan_negatif_diterima(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.perekonomian.store'), [
                'tahun'            => 2020,
                'pdrb_adhb'        => 469053301.48,
                'pdrb_adhk'        => 316172607.15,
                'laju_pertumbuhan' => -0.86,
            ])
            ->assertRedirect(route('admin.perekonomian.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('data_perekonomian', [
            'tahun'            => 2020,
            'laju_pertumbuhan' => -0.86,
        ]);
    }

    public function test_tahun_kembar_ditolak(): void
    {
        DataPerekonomian::create([
            'tahun' => 2024, 'pdrb_adhb' => 1, 'pdrb_adhk' => 1, 'laju_pertumbuhan' => 1,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.perekonomian.store'), [
                'tahun' => 2024, 'pdrb_adhb' => 2, 'pdrb_adhk' => 2, 'laju_pertumbuhan' => 2,
            ])
            ->assertSessionHasErrors('tahun');

        $this->assertSame(1, DataPerekonomian::where('tahun', 2024)->count());
    }

    /* ── CSV ──────────────────────────────────────────────────────────── */

    private function unggah(string $isi, string $route)
    {
        return $this->actingAs($this->admin())->post(route($route), [
            'file' => UploadedFile::fake()->createWithContent('data.csv', $isi),
        ]);
    }

    public function test_export_memuat_baris_yang_ada(): void
    {
        DataPerekonomian::create([
            'tahun' => 2024, 'pdrb_adhb' => 627869621.19,
            'pdrb_adhk' => 383113079.03, 'laju_pertumbuhan' => 5.27,
        ]);

        $isi = $this->actingAs($this->admin())
            ->get(route('admin.perekonomian.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('tahun,pdrb_adhb,pdrb_adhk,laju_pertumbuhan,sumber', $isi);
        $this->assertStringContainsString('2024,627869621.19', $isi);
    }

    public function test_import_membuat_dan_memperbarui_tanpa_menduplikat(): void
    {
        DataPerekonomian::create([
            'tahun' => 2024, 'pdrb_adhb' => 1, 'pdrb_adhk' => 1, 'laju_pertumbuhan' => 1,
        ]);

        $this->unggah(
            "tahun,pdrb_adhb,pdrb_adhk,laju_pertumbuhan,sumber\n"
            . "2024,627869621.19,383113079.03,5.27,BPS\n"
            . "2023,585463899.81,363931254.11,5.30,BPS\n",
            'admin.perekonomian.import'
        )->assertRedirect(route('admin.perekonomian.index'));

        $this->assertSame(2, DataPerekonomian::count());
        $this->assertSame('627869621.19', DataPerekonomian::where('tahun', 2024)->value('pdrb_adhb'));
    }

    /** Pemisah titik-koma ala Excel lokal harus tetap terbaca. */
    public function test_import_menerima_pemisah_titik_koma(): void
    {
        $this->unggah(
            "tahun;pdrb_adhb;pdrb_adhk;laju_pertumbuhan;sumber\n"
            . "2024;627869621,19;383113079,03;5,27;BPS\n",
            'admin.perekonomian.import'
        )->assertRedirect(route('admin.perekonomian.index'));

        $this->assertSame('627869621.19', DataPerekonomian::where('tahun', 2024)->value('pdrb_adhb'));
    }

    /**
     * Baris BARU yang kolom wajibnya belum lengkap harus dilewati dengan pesan,
     * bukan menjatuhkan impor karena kolom NOT NULL di database.
     */
    public function test_import_melewati_baris_baru_yang_tidak_lengkap(): void
    {
        $this->unggah(
            "tahun,pdrb_adhb,pdrb_adhk,laju_pertumbuhan\n"
            . "2024,627869621.19,,\n",
            'admin.perekonomian.import'
        )->assertRedirect(route('admin.perekonomian.index'));

        $this->assertSame(0, DataPerekonomian::count());
    }

    /** Header tanpa kolom kunci ditolak dengan pesan, bukan error 500. */
    public function test_import_menolak_header_tanpa_kolom_kunci(): void
    {
        $this->unggah("pdrb_adhb,pdrb_adhk\n1,2\n", 'admin.perekonomian.import')
            ->assertSessionHas('error');

        $this->assertSame(0, DataPerekonomian::count());
    }

    /** Kunci sektor terdiri dari dua kolom, jadi tahun sama boleh berulang. */
    public function test_import_sektor_memakai_kunci_tahun_dan_kode(): void
    {
        $this->unggah(
            "tahun,kode_sektor,kategori,nama_sektor,adhb,distribusi,laju_pertumbuhan\n"
            . "2024,7,G,Perdagangan,123886000,19.73,6.63\n"
            . "2024,10,J,Informasi dan Komunikasi,111137013,17.70,4.71\n",
            'admin.pdrb-sektor.import'
        )->assertRedirect(route('admin.perekonomian.index'));

        $this->assertSame(2, PdrbSektor::where('tahun', 2024)->count());
        $this->assertSame('Perdagangan', PdrbSektor::where('kode_sektor', 7)->value('nama_sektor'));
    }

    public function test_template_sektor_memuat_seluruh_kolom(): void
    {
        $isi = $this->actingAs($this->admin())
            ->get(route('admin.pdrb-sektor.template'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString(
            'tahun,kode_sektor,kategori,nama_sektor,adhb,distribusi,laju_pertumbuhan',
            $isi
        );
    }

    /** Kunci uniknya (tahun, kode_sektor) — sektor sama boleh ada di tahun berbeda. */
    public function test_sektor_kembar_pada_tahun_sama_ditolak(): void
    {
        PdrbSektor::create([
            'tahun' => 2024, 'kode_sektor' => 7, 'kategori' => 'G',
            'nama_sektor' => 'Perdagangan', 'adhb' => 100,
            'distribusi' => 19.73, 'laju_pertumbuhan' => 6.63,
        ]);

        $kirim = [
            'tahun' => 2024, 'kode_sektor' => 7, 'kategori' => 'G',
            'nama_sektor' => 'Perdagangan', 'adhb' => 200,
            'distribusi' => 19.73, 'laju_pertumbuhan' => 6.63,
        ];

        $this->actingAs($this->admin())
            ->post(route('admin.pdrb-sektor.store'), $kirim)
            ->assertSessionHasErrors('kode_sektor');

        $this->actingAs($this->admin())
            ->post(route('admin.pdrb-sektor.store'), ['tahun' => 2023] + $kirim)
            ->assertSessionHasNoErrors();

        $this->assertSame(2, PdrbSektor::where('kode_sektor', 7)->count());
    }
}
