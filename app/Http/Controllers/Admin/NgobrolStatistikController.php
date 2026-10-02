<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NgobrolStatistik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * CRUD konten Ngobrol Statistik: video, infografis, dan materi statistik.
 *
 * Satu controller untuk ketiganya karena tabelnya satu; yang beda hanya kolom
 * wajibnya, dan itu ditangani validated(). Tidak ada CSV maupun sinkronisasi:
 * kontennya hasil kurasi satu per satu.
 */
class NgobrolStatistikController extends Controller
{
    /** Batas unggah gambar (KB). Infografis memang tinggi-tinggi resolusinya. */
    private const MAKS_GAMBAR = 5120;

    public function index(Request $request)
    {
        $kategori = $request->get('kategori');
        if (!array_key_exists((string) $kategori, NgobrolStatistik::KATEGORI)) {
            $kategori = null;
        }

        return view('admin.ngobrol-statistik.index', [
            'daftar'         => NgobrolStatistik::kategori($kategori)->orderBy('kategori')->orderBy('urutan')->latest('id')->get(),
            'kategori'       => $kategori,
            'jumlahKategori' => NgobrolStatistik::selectRaw('kategori, count(*) c')
                ->groupBy('kategori')->pluck('c', 'kategori'),
        ]);
    }

    public function store(Request $request)
    {
        $kategori = $request->validate([
            'kategori' => ['required', Rule::in(array_keys(NgobrolStatistik::KATEGORI))],
        ])['kategori'];

        $data = $this->validated($request, $kategori, null);

        NgobrolStatistik::create($data + ['kategori' => $kategori]);

        return $this->kembali($kategori, 'ditambahkan');
    }

    /** Jenis konten tidak bisa diganti lewat edit — kolom wajibnya beda. */
    public function update(Request $request, NgobrolStatistik $ngobrolStatistik)
    {
        $ngobrolStatistik->update($this->validated($request, $ngobrolStatistik->kategori, $ngobrolStatistik));

        return $this->kembali($ngobrolStatistik->kategori, 'diperbarui');
    }

    public function destroy(NgobrolStatistik $ngobrolStatistik)
    {
        $ngobrolStatistik->hapusGambar();
        $ngobrolStatistik->delete();

        return $this->kembali($ngobrolStatistik->kategori, 'dihapus');
    }

    private function kembali(string $kategori, string $aksi)
    {
        return redirect()
            ->route('admin.ngobrol-statistik.index', ['kategori' => $kategori])
            ->with('success', NgobrolStatistik::KATEGORI[$kategori] . " {$aksi}.");
    }

    private function validated(Request $request, string $kategori, ?NgobrolStatistik $lama): array
    {
        $gambar = ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:' . self::MAKS_GAMBAR];

        $rules = [
            'judul'     => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'urutan'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'tampil'    => ['required', 'boolean'],
        ] + match ($kategori) {
            'video' => [
                'youtube_url' => ['required', 'string', 'max:500'],
            ],
            // Wajib saat tambah; saat edit boleh kosong = pakai gambar lama.
            'infografis' => [
                'gambar'        => array_merge([$lama ? 'nullable' : 'required'], $gambar),
                // Hanya domain instagram.com: tautan ini dibuka dari tombol
                // berikon Instagram, jadi tujuan lain akan menyesatkan.
                'instagram_url' => ['nullable', 'string', 'max:500', 'regex:~^https?://(www\.)?instagram\.com/\S+$~i'],
            ],
            'materi' => [
                'isi'          => ['required', 'string', 'max:60000'],
                'gambar'       => array_merge(['nullable'], $gambar),
                'hapus_gambar' => ['nullable', 'boolean'],
            ],
        };

        // Pesan gambar ditulis sendiri: input file tidak bisa diberi atribut
        // `required` di HTML (saat edit boleh kosong), jadi kesalahan ini
        // benar-benar sampai ke admin. "uploaded" muncul bila file melebihi
        // upload_max_filesize PHP, yang bisa lebih kecil dari MAKS_GAMBAR.
        $data = $request->validate($rules, [
            'gambar.required' => 'Gambar infografis wajib diunggah.',
            'gambar.uploaded' => 'Gambar gagal diunggah — kemungkinan melebihi batas ukuran unggahan server.',
            'gambar.max'      => 'Ukuran gambar maksimal 5 MB.',
            'instagram_url.regex' => 'Link Instagram harus berupa tautan instagram.com, mis. https://www.instagram.com/p/xxxxxxx/',
        ], [
            'youtube_url' => 'link YouTube',
            'isi'         => 'isi artikel',
        ]);

        $data['urutan'] = (int) ($data['urutan'] ?? 0);

        if ($kategori === 'video') {
            $id = NgobrolStatistik::parseYoutubeId($data['youtube_url']);
            if ($id === null) {
                throw ValidationException::withMessages([
                    'youtube_url' => 'Link YouTube tidak dikenali. Tempel link video, mis. https://www.youtube.com/watch?v=xxxxxxxxxxx',
                ]);
            }
            $data['youtube_id'] = $id;
            unset($data['youtube_url']);
        }

        // Gambar baru menggantikan yang lama; file lama dibuang supaya folder
        // unggahan tidak menumpuk sampah.
        unset($data['gambar'], $data['hapus_gambar']);
        if ($request->hasFile('gambar')) {
            $lama?->hapusGambar();
            $data['gambar'] = $request->file('gambar')->store(NgobrolStatistik::FOLDER, 'public');
        } elseif ($lama && $kategori === 'materi' && $request->boolean('hapus_gambar')) {
            $lama->hapusGambar();
            $data['gambar'] = null;
        }

        return $data;
    }
}
