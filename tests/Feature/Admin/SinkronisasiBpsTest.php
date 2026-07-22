<?php

namespace Tests\Feature\Admin;

use App\Models\DataIklim;
use App\Models\DataKemiskinan;
use App\Models\DataPerekonomian;
use App\Models\Kecamatan;
use App\Models\PdrbSektor;
use App\Models\User;
use App\Services\Statistik\SinkronisasiBps;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tombol "Sinkronkan BPS" di portal admin.
 *
 * Yang paling penting diuji di sini bukan jalur suksesnya, melainkan jaminan
 * bahwa sinkronisasi TIDAK PERNAH menghapus data. Sebelumnya seeder-seeder ini
 * memakai truncate(), sehingga sekali dijalankan seluruh tabel dikosongkan —
 * termasuk baris yang ditambal manual operator. Tombol di portal membuat
 * perilaku itu bisa dipicu siapa pun yang bisa login, jadi sifat
 * non-destruktifnya harus dikunci tes.
 */
class SinkronisasiBpsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    /**
     * BPS dibalas dengan badan kosong yang SAH (200, tanpa datacontent) —
     * meniru "tahun belum dirilis". Memakai status 200 juga menghindari
     * mekanisme retry BpsClient yang akan memperlambat tes.
     */
    private function bpsKosong(): void
    {
        Http::preventStrayRequests();
        Http::fake(['*' => Http::response(['status' => 'OK', 'data' => [], 'datacontent' => []], 200)]);
    }

    /**
     * BPS membalas dengan data kemiskinan yang sah untuk tahun-tahun yang
     * diminta seeder. Ini penting: dengan balasan KOSONG, seeder berhenti di
     * penjaga awal dan tidak pernah menyentuh tabel — sehingga tes menjadi
     * hijau tanpa benar-benar menguji jalur penulisannya.
     *
     * Key datacontent BPS berformat {vervar}{var}{turvar}{th}{turth};
     * 5 = Jakarta Barat, 117 = variabel kemiskinan, turvar 58-62.
     */
    private function bpsKemiskinanBerisi(): void
    {
        // Tanpa key, BpsClient sengaja berhenti sebelum mengirim permintaan —
        // jadi tes tidak akan pernah sampai ke jalur penulisan.
        config(['statistik.bps.key' => 'kunci-uji']);

        $datacontent = [];
        foreach (['122', '123', '124', '125'] as $th) {
            $datacontent["511758{$th}0"] = 88.5;    // jumlah penduduk miskin (ribu)
            $datacontent["511759{$th}0"] = 3.9;     // persentase
            $datacontent["511760{$th}0"] = 700000;  // garis kemiskinan
            $datacontent["511761{$th}0"] = 0.5;     // P1
            $datacontent["511762{$th}0"] = 0.1;     // P2
        }

        Http::preventStrayRequests();
        Http::fake(['*' => Http::response(['status' => 'OK', 'datacontent' => $datacontent], 200)]);
    }

    /* ── Jaminan utama: tidak menghapus apa pun ───────────────────────── */

    /**
     * Regresi atas perilaku lama: KemiskinanSeeder dulu memanggil truncate()
     * sebelum menulis, sehingga sekali sinkronisasi berjalan, tahun yang
     * diinput manual operator dan tidak ada di BPS ikut lenyap.
     *
     * 2019 sengaja dipilih karena di luar rentang TAHUN_BPS seeder — persis
     * kasus "data lama yang hanya ada di portal".
     */
    public function test_tahun_yang_diisi_manual_selamat_saat_sinkronisasi_menulis(): void
    {
        $this->bpsKemiskinanBerisi();

        DataKemiskinan::create([
            'tahun' => 2019, 'jumlah_penduduk_miskin' => 99999,
            'persentase_penduduk_miskin' => 4.1, 'garis_kemiskinan' => 700000,
            'indeks_kedalaman' => 0.5, 'indeks_keparahan' => 0.1,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.sinkronisasi', 'kemiskinan'))
            ->assertRedirect();

        // Tahun dari BPS masuk...
        $this->assertTrue(DataKemiskinan::where('tahun', 2022)->exists());
        // ...dan tahun manual tetap utuh, bukan terhapus.
        $this->assertSame(99999, DataKemiskinan::where('tahun', 2019)->value('jumlah_penduduk_miskin'));
    }

    /** Sinkronisasi ulang memperbarui baris yang sama, bukan menambah kembar. */
    public function test_sinkronisasi_kedua_memperbarui_bukan_menggandakan(): void
    {
        $this->bpsKemiskinanBerisi();

        $this->actingAs($this->admin())->post(route('admin.sinkronisasi', 'kemiskinan'));
        $jumlah = DataKemiskinan::count();

        $this->actingAs($this->admin())
            ->post(route('admin.sinkronisasi', 'kemiskinan'))
            ->assertSessionHas('success');

        $this->assertSame($jumlah, DataKemiskinan::count());
        $this->assertStringContainsString('diperbarui', session('success'));
    }

    public function test_sinkronisasi_tidak_menghapus_data_saat_bps_kosong(): void
    {
        $this->bpsKosong();

        DataKemiskinan::create([
            'tahun' => 2024, 'jumlah_penduduk_miskin' => 99999,
            'persentase_penduduk_miskin' => 4.1, 'garis_kemiskinan' => 700000,
            'indeks_kedalaman' => 0.5, 'indeks_keparahan' => 0.1,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.sinkronisasi', 'kemiskinan'))
            ->assertRedirect();

        $this->assertSame(1, DataKemiskinan::count());
        $this->assertSame(99999, DataKemiskinan::first()->jumlah_penduduk_miskin);
    }

    /** Berlaku untuk semua modul, bukan cuma kemiskinan. */
    public function test_semua_modul_mempertahankan_data_yang_ada(): void
    {
        $this->bpsKosong();

        DataIklim::create([
            'tahun' => 2024, 'bulan' => 1, 'hari_hujan' => 18, 'tekanan_udara' => 1009.2,
            'suhu_udara' => 27.6, 'kecepatan_angin' => 2.4,
            'kelembaban_udara' => 81, 'penyinaran_matahari' => 45,
        ]);
        DataPerekonomian::create([
            'tahun' => 2024, 'pdrb_adhb' => 1, 'pdrb_adhk' => 1, 'laju_pertumbuhan' => 1,
        ]);
        PdrbSektor::create([
            'tahun' => 2024, 'kode_sektor' => 7, 'kategori' => 'G',
            'nama_sektor' => 'Perdagangan', 'adhb' => 100,
            'distribusi' => 19.73, 'laju_pertumbuhan' => 6.63,
        ]);

        foreach (array_keys(SinkronisasiBps::daftarModul()) as $modul) {
            $this->actingAs($this->admin())->post(route('admin.sinkronisasi', $modul));
        }

        $this->assertSame(1, DataIklim::count());
        $this->assertSame(1, DataPerekonomian::count());
        $this->assertSame(1, PdrbSektor::count());
    }

    /**
     * Kecamatan adalah master yang direferensikan 9 modul dengan
     * cascadeOnDelete — menghapusnya lalu membuat ulang akan melenyapkan
     * seluruh data anak. Sinkronisasi geografis harus memakai id yang sama.
     */
    public function test_sinkronisasi_geografis_tidak_membuat_ulang_kecamatan(): void
    {
        $this->bpsKosong();

        $this->actingAs($this->admin())->post(route('admin.sinkronisasi', 'geografis'));
        $idAwal = Kecamatan::orderBy('nama_kecamatan')->pluck('id', 'nama_kecamatan');

        $this->actingAs($this->admin())->post(route('admin.sinkronisasi', 'geografis'));
        $idAkhir = Kecamatan::orderBy('nama_kecamatan')->pluck('id', 'nama_kecamatan');

        $this->assertEquals($idAwal->all(), $idAkhir->all());
        $this->assertSame(8, Kecamatan::count());
    }

    /** Dijalankan dua kali tidak boleh menggandakan baris. */
    public function test_sinkronisasi_idempoten(): void
    {
        $this->bpsKosong();

        $this->actingAs($this->admin())->post(route('admin.sinkronisasi', 'geografis'));
        $jumlah = \App\Models\LuasKecamatan::count();

        $this->actingAs($this->admin())->post(route('admin.sinkronisasi', 'geografis'));

        $this->assertSame($jumlah, \App\Models\LuasKecamatan::count());
    }

    /* ── Umpan balik ke operator ──────────────────────────────────────── */

    public function test_melaporkan_ketika_tidak_ada_data_baru(): void
    {
        $this->bpsKosong();

        $this->actingAs($this->admin())
            ->post(route('admin.sinkronisasi', 'kemiskinan'))
            ->assertSessionHas('success');

        $this->assertStringContainsString('tidak ada data baru', session('success'));
    }

    public function test_melaporkan_jumlah_baris_yang_tersentuh(): void
    {
        $this->bpsKosong();

        // Geografis menulis kecamatan + luas dari daftar tetap, jadi selalu ada
        // baris yang dibuat walau BPS sedang kosong.
        $this->actingAs($this->admin())
            ->post(route('admin.sinkronisasi', 'geografis'))
            ->assertSessionHas('success');

        $this->assertStringContainsString('ditambahkan', session('success'));
        $this->assertStringContainsString('tidak dihapus', session('success'));
    }

    /* ── Akses & bentuk route ─────────────────────────────────────────── */

    public function test_tamu_tidak_bisa_memicu_sinkronisasi(): void
    {
        $this->post(route('admin.sinkronisasi', 'kemiskinan'))
            ->assertRedirect(route('admin.login'));

        $this->assertSame(0, Kecamatan::count());
    }

    public function test_modul_tak_dikenal_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.sinkronisasi', 'bukan-modul'))
            ->assertNotFound();
    }

    /** Setiap modul yang terdaftar harus punya tombolnya di halaman admin. */
    public function test_setiap_modul_terdaftar_punya_tombol_di_halamannya(): void
    {
        $halaman = [
            'geografis'    => 'admin.geografis.index',
            'iklim'        => 'admin.iklim.index',
            'kependudukan' => 'admin.kependudukan.index',
            'pendidikan'   => 'admin.pendidikan.index',
            'kesehatan'    => 'admin.kesehatan.index',
            'kemiskinan'   => 'admin.kemiskinan.index',
            'perekonomian' => 'admin.perekonomian.index',
        ];

        $this->assertSame(
            array_keys(SinkronisasiBps::daftarModul()),
            array_keys($halaman),
            'Ada modul terdaftar yang belum dipetakan ke halaman admin.',
        );

        foreach ($halaman as $modul => $route) {
            $this->actingAs($this->admin())
                ->get(route($route))
                ->assertOk()
                ->assertSee('Sinkronkan BPS')
                ->assertSee(route('admin.sinkronisasi', $modul), false);
        }
    }
}
