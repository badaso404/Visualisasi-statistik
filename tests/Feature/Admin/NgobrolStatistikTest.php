<?php

namespace Tests\Feature\Admin;

use App\Models\NgobrolStatistik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NgobrolStatistikTest extends TestCase
{
    use RefreshDatabase;

    /** @dataProvider formatDokumen */
    public function test_admin_dapat_mengunggah_dokumen_materi(string $nama, string $mime): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->post(route('admin.ngobrol-statistik.store'), [
                'kategori' => 'materi',
                'judul' => 'Materi presentasi',
                'isi' => 'Isi materi statistik.',
                'urutan' => 0,
                'tampil' => 1,
                'presentasi' => UploadedFile::fake()->create(
                    $nama,
                    100,
                    $mime
                ),
            ])
            ->assertRedirect(route('admin.ngobrol-statistik.index', ['kategori' => 'materi']));

        $materi = NgobrolStatistik::where('judul', 'Materi presentasi')->firstOrFail();

        $this->assertStringEndsWith(pathinfo($nama, PATHINFO_EXTENSION), $materi->presentasi);
        $this->assertTrue(Storage::disk('public')->exists($materi->presentasi));

        $this->get(route('admin.ngobrol-statistik.index', ['kategori' => 'materi']))
            ->assertOk()
            ->assertSee('Dokumen tersedia');
    }

    public static function formatDokumen(): array
    {
        return [
            'PDF' => ['materi.pdf', 'application/pdf'],
            'PPT' => ['materi.ppt', 'application/vnd.ms-powerpoint'],
            'PPTX' => ['materi.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        ];
    }

    public function test_satu_upload_infografis_membuat_satu_record(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.ngobrol-statistik.index'))
            ->assertOk()
            ->assertSee('data-submit-once', false);

        $this->actingAs($admin)
            ->post(route('admin.ngobrol-statistik.store'), [
                'kategori' => 'infografis',
                'judul' => 'Infografis uji satu kali',
                'urutan' => 0,
                'tampil' => 1,
                'gambar' => UploadedFile::fake()->image('infografis.png'),
            ])
            ->assertRedirect(route('admin.ngobrol-statistik.index', ['kategori' => 'infografis']));

        $this->assertDatabaseCount('ngobrol_statistik', 1);
        $this->get(route('statistik.infografis'))
            ->assertOk()
            ->assertSee('Infografis uji satu kali');
    }
}
