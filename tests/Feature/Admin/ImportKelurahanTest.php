<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\PendudukKelurahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\MembuatRingkasanInduk;
use Tests\TestCase;

/**
 * Import kelurahan punya implementasi sendiri (bukan trait CsvPerKecamatan)
 * karena mencocokkan baris per nama_kelurahan + tahun dan ikut membawa lat/lng.
 */
class ImportKelurahanTest extends TestCase
{
    use RefreshDatabase;
    use MembuatRingkasanInduk;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function csv(string $isi): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'csv') . '.csv';
        file_put_contents($path, $isi);

        return new UploadedFile($path, 'kelurahan.csv', 'text/csv', null, true);
    }

    public function test_import_membuat_baris_baru(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);

        $csv = "kecamatan,tahun,nama_kelurahan,latitude,longitude,jumlah_penduduk\n"
             . "Kebon Jeruk,2025,Kelapa Dua,-6.209248,106.768570,52000\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.import'), ['file' => $this->csv($csv)])
            ->assertRedirect(route('admin.kependudukan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('penduduk_kelurahan', [
            'nama_kelurahan'  => 'Kelapa Dua',
            'tahun'           => 2025,
            'jumlah_penduduk' => 52000,
        ]);
    }

    public function test_import_memperbarui_termasuk_koordinat(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        $k = Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);
        PendudukKelurahan::create([
            'kecamatan_id'    => $k->id,
            'tahun'           => 2025,
            'nama_kelurahan'  => 'Kelapa Dua',
            'jumlah_penduduk' => 1,
            'latitude'        => null,
            'longitude'       => null,
        ]);

        $csv = "kecamatan,tahun,nama_kelurahan,latitude,longitude,jumlah_penduduk\n"
             . "Kebon Jeruk,2025,Kelapa Dua,-6.209248,106.768570,52000\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.import'), ['file' => $this->csv($csv)]);

        $this->assertDatabaseCount('penduduk_kelurahan', 1);

        $row = PendudukKelurahan::first();
        $this->assertSame(52000, (int) $row->jumlah_penduduk);
        $this->assertNotNull($row->latitude, 'lat/lng harus ikut diperbarui');
    }

    public function test_import_melewati_baris_bermasalah_tapi_menyimpan_sisanya(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);

        $csv = "kecamatan,tahun,nama_kelurahan,latitude,longitude,jumlah_penduduk\n"
             . "TidakDikenal,2025,Anu,,,100\n"
             . "Kebon Jeruk,2025,Kelapa Dua,,,52000\n"
             . "\n"
             . "Kebon Jeruk,1800,Tahun Ngawur,,,10\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.import'), ['file' => $this->csv($csv)])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('penduduk_kelurahan', 1);
        $this->assertDatabaseHas('penduduk_kelurahan', ['nama_kelurahan' => 'Kelapa Dua']);
    }

    public function test_import_menerima_pemisah_titik_koma(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);

        $csv = "kecamatan;tahun;nama_kelurahan;latitude;longitude;jumlah_penduduk\n"
             . "Kebon Jeruk;2025;Kelapa Dua;;;52000\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.import'), ['file' => $this->csv($csv)]);

        $this->assertDatabaseHas('penduduk_kelurahan', ['nama_kelurahan' => 'Kelapa Dua']);
    }

    public function test_import_menolak_header_tanpa_kolom_wajib(): void
    {
        $csv = "provinsi,jumlah\nDKI,100\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.import'), ['file' => $this->csv($csv)])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('penduduk_kelurahan', 0);
    }

    /** Baris 32 controller ini ditandai IDE sebagai "kurang argumen" — dipastikan jalan. */
    public function test_hapus_baris_kelurahan(): void
    {
        $k   = Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);
        $row = PendudukKelurahan::create([
            'kecamatan_id'    => $k->id,
            'tahun'           => 2025,
            'nama_kelurahan'  => 'Kelapa Dua',
            'jumlah_penduduk' => 52000,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.penduduk-kelurahan.destroy', $row))
            ->assertRedirect(route('admin.kependudukan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('penduduk_kelurahan', ['id' => $row->id]);
    }

    public function test_tambah_dan_ubah_baris_kelurahan(): void
    {
        $this->indukKependudukan(2024, 2025, 2026);

        $k = Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kelurahan.store'), [
                'kecamatan_id'    => $k->id,
                'tahun'           => 2025,
                'nama_kelurahan'  => 'Kelapa Dua',
                'jumlah_penduduk' => 52000,
            ])
            ->assertSessionHasNoErrors();

        $row = PendudukKelurahan::firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('admin.penduduk-kelurahan.update', $row), [
                'kecamatan_id'    => $k->id,
                'tahun'           => 2025,
                'nama_kelurahan'  => 'Kelapa Dua',
                'jumlah_penduduk' => 60000,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penduduk_kelurahan', ['id' => $row->id, 'jumlah_penduduk' => 60000]);
    }

    public function test_export_dan_template_terunduh(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Kebon Jeruk']);
        PendudukKelurahan::create([
            'kecamatan_id'    => $k->id,
            'tahun'           => 2025,
            'nama_kelurahan'  => 'Kelapa Dua',
            'jumlah_penduduk' => 52000,
        ]);

        $isi = $this->actingAs($this->admin())
            ->get(route('admin.penduduk-kelurahan.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('Kelapa Dua', $isi);

        $this->actingAs($this->admin())
            ->get(route('admin.penduduk-kelurahan.template'))
            ->assertOk();
    }
}
