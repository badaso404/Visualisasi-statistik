<?php

namespace Tests\Feature\Admin;

use App\Models\FasilitasUmum;
use App\Models\Kecamatan;
use App\Models\PendudukKelurahan;
use App\Models\User;
use App\Services\Statistik\FasilitasJakbarSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FasilitasUmumTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function kecamatan(string $nama = 'Kalideres'): Kecamatan
    {
        return Kecamatan::create(['nama_kecamatan' => $nama]);
    }

    // ── CRUD ─────────────────────────────────────────────────────

    public function test_bisa_menambah_fasilitas(): void
    {
        $kec = $this->kecamatan();

        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.store'), [
                'kategori'     => 'olahraga',
                'nama'         => 'GOR Kalideres',
                'alamat'       => 'Jl. Peta Utara II No.1',
                'kecamatan_id' => $kec->id,
                'latitude'     => '-6.1400000',
                'longitude'    => '106.7000000',
            ])
            ->assertRedirect(route('admin.fasilitas-umum.index'));

        $this->assertDatabaseHas('fasilitas_umum', [
            'kategori'     => 'olahraga',
            'nama'         => 'GOR Kalideres',
            'kecamatan_id' => $kec->id,
        ]);
    }

    public function test_kategori_di_luar_daftar_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.store'), ['kategori' => 'ngawur', 'nama' => 'X'])
            ->assertSessionHasErrors('kategori');

        $this->assertDatabaseCount('fasilitas_umum', 0);
    }

    public function test_koordinat_di_luar_batas_bumi_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.store'), [
                'kategori' => 'rptra',
                'nama'     => 'RPTRA Uji',
                'latitude' => '999',
            ])
            ->assertSessionHasErrors('latitude');
    }

    /** Kecamatan boleh kosong: sebagian data sumber memang tak menyebut wilayah. */
    public function test_fasilitas_boleh_tanpa_kecamatan(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.store'), [
                'kategori' => 'pemadam-kebakaran',
                'nama'     => 'Pos Tanpa Wilayah',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fasilitas_umum', ['nama' => 'Pos Tanpa Wilayah', 'kecamatan_id' => null]);
    }

    public function test_bisa_mengubah_dan_menghapus(): void
    {
        $f = FasilitasUmum::create(['kategori' => 'rptra', 'nama' => 'RPTRA Lama']);

        $this->actingAs($this->admin())
            ->put(route('admin.fasilitas-umum.update', $f), ['kategori' => 'rptra', 'nama' => 'RPTRA Baru'])
            ->assertRedirect(route('admin.fasilitas-umum.index'));
        $this->assertSame('RPTRA Baru', $f->fresh()->nama);

        $this->actingAs($this->admin())
            ->delete(route('admin.fasilitas-umum.destroy', $f))
            ->assertRedirect(route('admin.fasilitas-umum.index'));
        $this->assertDatabaseCount('fasilitas_umum', 0);
    }

    /**
     * Kecamatan dihapus dari master tidak boleh ikut menghapus fasilitasnya —
     * kolomnya nullOnDelete, bukan cascade.
     */
    public function test_menghapus_kecamatan_menyisakan_fasilitas_tanpa_kaitan(): void
    {
        $kec = $this->kecamatan();
        $f   = FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'GOR X', 'kecamatan_id' => $kec->id]);

        $kec->delete();

        $this->assertDatabaseHas('fasilitas_umum', ['id' => $f->id, 'kecamatan_id' => null]);
    }

    // ── Penyaringan ──────────────────────────────────────────────

    public function test_penyaring_kategori_dan_pencarian(): void
    {
        FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'GOR Tambora']);
        FasilitasUmum::create(['kategori' => 'rptra', 'nama' => 'RPTRA Alur Dahlia']);

        $this->actingAs($this->admin())
            ->get(route('admin.fasilitas-umum.index', ['kategori' => 'rptra']))
            ->assertOk()
            ->assertSee('RPTRA Alur Dahlia')
            ->assertDontSee('GOR Tambora');

        $this->actingAs($this->admin())
            ->get(route('admin.fasilitas-umum.index', ['q' => 'Tambora']))
            ->assertOk()
            ->assertSee('GOR Tambora')
            ->assertDontSee('RPTRA Alur Dahlia');
    }

    public function test_penyaring_kecamatan_kosong_menampilkan_yang_belum_terpetakan(): void
    {
        $kec = $this->kecamatan();
        FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'Punya Kecamatan', 'kecamatan_id' => $kec->id]);
        FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'Belum Terpetakan']);

        $this->actingAs($this->admin())
            ->get(route('admin.fasilitas-umum.index', ['kecamatan_id' => 'kosong']))
            ->assertOk()
            ->assertSee('Belum Terpetakan')
            ->assertDontSee('Punya Kecamatan');
    }

    // ── CSV ──────────────────────────────────────────────────────

    public function test_impor_csv_melengkapi_koordinat_tanpa_menduplikat(): void
    {
        $kec = $this->kecamatan();
        FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'GOR Kalideres', 'sumber_id' => 99]);

        $csv = "kategori,nama,alamat,kecamatan,kelurahan,latitude,longitude\n"
            . "olahraga,GOR Kalideres,Jl. Peta Utara,{$kec->nama_kecamatan},Pegadungan,-6.14,106.7\n";

        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.import'), [
                'file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('f.csv', $csv),
            ])
            ->assertRedirect(route('admin.fasilitas-umum.index'));

        // Dicocokkan pada (kategori + nama), jadi barisnya diperbarui — bukan
        // ditambah lagi — dan sumber_id aslinya tetap utuh.
        $this->assertDatabaseCount('fasilitas_umum', 1);
        $this->assertDatabaseHas('fasilitas_umum', [
            'nama'         => 'GOR Kalideres',
            'sumber_id'    => 99,
            'kecamatan_id' => $kec->id,
            'kelurahan'    => 'Pegadungan',
        ]);
    }

    public function test_impor_csv_menolak_kategori_tak_dikenal(): void
    {
        $csv = "kategori,nama\nngawur,Fasilitas Aneh\n";

        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.import'), [
                'file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('f.csv', $csv),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('fasilitas_umum', 0);
    }

    public function test_export_csv_memuat_seluruh_kolom(): void
    {
        FasilitasUmum::create(['kategori' => 'rptra', 'nama' => 'RPTRA Alur Dahlia']);

        $isi = $this->actingAs($this->admin())
            ->get(route('admin.fasilitas-umum.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('kategori,nama,alamat,kecamatan,kelurahan,latitude,longitude', $isi);
        $this->assertStringContainsString('RPTRA Alur Dahlia', $isi);
    }

    // ── Sinkronisasi ─────────────────────────────────────────────

    /**
     * Sumber tidak punya kolom kecamatan; yang ada kolom `wilayah` berisi akun
     * pengunggah. Ini menguji ketiga jalur penebakan sekaligus.
     */
    public function test_sinkronisasi_memetakan_kecamatan_dari_wilayah_alamat_dan_nama(): void
    {
        $kalideres = $this->kecamatan('Kalideres');
        $tambora   = $this->kecamatan('Tambora');
        $kebonJeruk = $this->kecamatan('Kebon Jeruk');

        // Peta kelurahan dipinjam dari tabel penduduk_kelurahan.
        PendudukKelurahan::create([
            'kecamatan_id' => $kalideres->id, 'tahun' => 2024,
            'nama_kelurahan' => 'Tegal Alur', 'jumlah_penduduk' => 100,
        ]);

        Http::fake([
            '*fasilitas/olahraga*' => Http::response([
                'total' => 3, 'per_page' => 10, 'data' => [
                    // lewat kolom wilayah "Kelurahan …"
                    ['id' => 1, 'nama_layanan' => "GOR Tegal Alur\t", 'alamat_layanan' => 'Jl. A', 'wilayah' => 'Kelurahan Tegal Alur', 'foto' => null],
                    // lewat alamat "Kec. …"
                    ['id' => 2, 'nama_layanan' => 'GOR Dua', 'alamat_layanan' => 'Jl. B, Kec. Tambora, Jakarta Barat', 'wilayah' => 'Super Admin', 'foto' => null],
                    // lewat singkatan pada nama, tanpa petunjuk lain
                    ['id' => 3, 'nama_layanan' => 'Sektor Kb. Jeruk', 'alamat_layanan' => 'Jl. C', 'wilayah' => 'Super Admin', 'foto' => null],
                ],
            ]),
        ]);

        $hasil = app(FasilitasJakbarSync::class)->jalankan(['olahraga']);

        $this->assertSame(3, $hasil['ditambah']);
        $this->assertSame(0, $hasil['tanpa_kecamatan']);
        $this->assertNull($hasil['error']);

        $this->assertDatabaseHas('fasilitas_umum', [
            // spasi & tab di ujung nilai sumber ikut dibersihkan
            'nama' => 'GOR Tegal Alur', 'kecamatan_id' => $kalideres->id, 'kelurahan' => 'Tegal Alur',
        ]);
        $this->assertDatabaseHas('fasilitas_umum', ['nama' => 'GOR Dua', 'kecamatan_id' => $tambora->id]);
        $this->assertDatabaseHas('fasilitas_umum', ['nama' => 'Sektor Kb. Jeruk', 'kecamatan_id' => $kebonJeruk->id]);
    }

    /** Sumber pernah mengirim id yang sama di dua halaman; jangan sampai dobel. */
    public function test_sinkronisasi_berulang_tidak_menduplikat(): void
    {
        Http::fake([
            '*fasilitas/perpustakaan*' => Http::response([
                'total' => 1, 'per_page' => 10, 'data' => [
                    ['id' => 7, 'nama_layanan' => 'Perpustakaan Kota', 'alamat_layanan' => 'Jl. Z', 'wilayah' => 'Super Admin', 'foto' => null],
                ],
            ]),
        ]);

        $sync = app(FasilitasJakbarSync::class);
        $sync->jalankan(['perpustakaan']);
        $hasil = $sync->jalankan(['perpustakaan']);

        $this->assertDatabaseCount('fasilitas_umum', 1);
        $this->assertSame(0, $hasil['ditambah']);
        $this->assertSame(1, $hasil['diperbarui']);
    }

    /** Kegagalan satu kategori tidak boleh membatalkan kategori lain. */
    public function test_kategori_yang_gagal_dilaporkan_tanpa_membatalkan_sisanya(): void
    {
        Http::fake([
            '*fasilitas/perpustakaan*' => Http::response([
                'total' => 1, 'per_page' => 10, 'data' => [
                    ['id' => 7, 'nama_layanan' => 'Perpustakaan Kota', 'alamat_layanan' => 'Jl. Z', 'wilayah' => 'Super Admin', 'foto' => null],
                ],
            ]),
            '*fasilitas/olahraga*' => Http::response('Too Many Requests', 429),
        ]);

        $hasil = app(FasilitasJakbarSync::class)->jalankan(['perpustakaan', 'olahraga']);

        $this->assertSame(1, $hasil['ditambah']);
        $this->assertNotNull($hasil['error']);
        // Pesan 429 sumber berupa halaman HTML panjang; yang tampil ke admin
        // harus ringkas, bukan seluruh badan balasan.
        $this->assertStringContainsString('429', $hasil['error']);
        $this->assertLessThan(200, strlen($hasil['error']));
        $this->assertDatabaseHas('fasilitas_umum', ['nama' => 'Perpustakaan Kota']);
    }

    public function test_tombol_sinkronisasi_admin_menolak_kategori_tak_dikenal(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.fasilitas-umum.sync'), ['kategori' => 'ngawur'])
            ->assertSessionHasErrors('kategori');
    }

    // ── Halaman publik ───────────────────────────────────────────

    public function test_halaman_publik_menampilkan_fasilitas_dan_hitungannya(): void
    {
        $kec = $this->kecamatan('Tambora');
        FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'GOR Tambora', 'kecamatan_id' => $kec->id]);
        FasilitasUmum::create(['kategori' => 'rptra', 'nama' => 'RPTRA Angke', 'kecamatan_id' => $kec->id]);

        $this->get(route('statistik.fasilitas-umum'))
            ->assertOk()
            ->assertSee('GOR Tambora')
            ->assertSee('RPTRA Angke')
            ->assertSee('Tambora');
    }

    /**
     * API sumber tidak mengirim koordinat sama sekali, jadi kondisi normal
     * sesudah sinkronisasi adalah nol titik. Peta kosong tanpa keterangan
     * gampang dibaca sebagai "tidak ada fasilitas".
     */
    public function test_tanpa_koordinat_peta_diganti_keterangan(): void
    {
        FasilitasUmum::create(['kategori' => 'olahraga', 'nama' => 'GOR Tanpa Koordinat']);

        $this->get(route('statistik.fasilitas-umum'))
            ->assertOk()
            ->assertSee('map-empty', false)
            ->assertDontSee('id="map-fasilitas"', false);
    }

    public function test_dengan_koordinat_peta_ditampilkan(): void
    {
        FasilitasUmum::create([
            'kategori' => 'olahraga', 'nama' => 'GOR Berkoordinat',
            'latitude' => -6.14, 'longitude' => 106.7,
        ]);

        $this->get(route('statistik.fasilitas-umum'))
            ->assertOk()
            ->assertSee('id="map-fasilitas"', false);
    }
}
