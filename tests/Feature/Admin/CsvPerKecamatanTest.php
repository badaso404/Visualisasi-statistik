<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\PendidikanKecamatan;
use App\Models\PendudukKecamatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CsvPerKecamatanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function csv(string $isi): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'csv') . '.csv';
        file_put_contents($path, $isi);

        return new UploadedFile($path, 'data.csv', 'text/csv', null, true);
    }

    public function test_template_memuat_kolom_sesuai_definisi_field(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('admin.pendidikan-kecamatan.template'))
            ->assertOk();

        $isi = $response->streamedContent();

        $this->assertStringContainsString('kecamatan', $isi);
        $this->assertStringContainsString('jumlah_pelajar', $isi);
        $this->assertStringContainsString('jumlah_sekolah_swasta', $isi);
    }

    public function test_export_memuat_data_yang_ada(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendudukKecamatan::create(['kecamatan_id' => $k->id, 'tahun' => 2025, 'jumlah_penduduk' => 12345]);

        $isi = $this->actingAs($this->admin())
            ->get(route('admin.penduduk-kecamatan.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('Cakung', $isi);
        $this->assertStringContainsString('12345', $isi);
    }

    public function test_import_membuat_dan_memperbarui_baris(): void
    {
        $k = Kecamatan::create(['nama_kecamatan' => 'Cakung']);
        PendidikanKecamatan::create([
            'kecamatan_id'          => $k->id,
            'tahun'                 => 2025,
            'jumlah_pelajar'        => 1,
            'jumlah_pendidik'       => 1,
            'jumlah_sekolah_negeri' => 1,
            'jumlah_sekolah_swasta' => 1,
        ]);

        $csv = "kecamatan,tahun,jumlah_pelajar,jumlah_pendidik,jumlah_sekolah_negeri,jumlah_sekolah_swasta\n"
             . "Cakung,2025,5000,300,20,15\n";

        $this->actingAs($this->admin())
            ->post(route('admin.pendidikan-kecamatan.import'), ['file' => $this->csv($csv)])
            ->assertRedirect(route('admin.pendidikan.index'));

        // Diperbarui, bukan ditambah sebagai baris baru.
        $this->assertDatabaseCount('pendidikan_kecamatan', 1);
        $this->assertDatabaseHas('pendidikan_kecamatan', ['tahun' => 2025, 'jumlah_pelajar' => 5000]);
    }

    public function test_import_menerima_pemisah_titik_koma(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $csv = "kecamatan;tahun;jumlah_penduduk\nCakung;2025;9999\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.import'), ['file' => $this->csv($csv)]);

        $this->assertDatabaseHas('penduduk_kecamatan', ['tahun' => 2025, 'jumlah_penduduk' => 9999]);
    }

    public function test_import_melewati_kecamatan_tak_dikenal(): void
    {
        Kecamatan::create(['nama_kecamatan' => 'Cakung']);

        $csv = "kecamatan,tahun,jumlah_penduduk\nEntahDimana,2025,500\nCakung,2025,700\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.import'), ['file' => $this->csv($csv)]);

        $this->assertDatabaseCount('penduduk_kecamatan', 1);
        $this->assertDatabaseHas('penduduk_kecamatan', ['jumlah_penduduk' => 700]);
    }

    public function test_import_menolak_header_tanpa_kolom_wajib(): void
    {
        $csv = "provinsi,jumlah\nDKI,100\n";

        $this->actingAs($this->admin())
            ->post(route('admin.penduduk-kecamatan.import'), ['file' => $this->csv($csv)])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('penduduk_kecamatan', 0);
    }
}
