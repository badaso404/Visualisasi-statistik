<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class NgobrolStatistik extends Model
{
    protected $table = 'ngobrol_statistik';

    protected $fillable = [
        'kategori',
        'judul',
        'deskripsi',
        'youtube_id',
        'gambar',
        'presentasi',
        'instagram_url',
        'isi',
        'urutan',
        'tampil',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'tampil' => 'boolean',
    ];

    /** Folder unggahan di disk "public". */
    public const FOLDER = 'ngobrol-statistik';

    /**
     * Jenis konten: slug => label Indonesia (dipakai panel admin). Label
     * publiknya lewat berkas bahasa (ngobrol.kategori.*) supaya ikut EN/ID.
     * Urutan di sini = urutan seksi di halaman publik.
     *
     * @var array<string, string>
     */
    public const KATEGORI = [
        'video'      => 'Video',
        'infografis' => 'Infografis',
        'materi'     => 'Materi',
    ];

    public const IKON = [
        'video'      => 'fa-circle-play',
        'infografis' => 'fa-chart-pie',
        'materi'     => 'fa-book-open',
    ];

    public function labelKategori(): string
    {
        $kunci = 'ngobrol.kategori.' . $this->kategori;

        return Lang::has($kunci) ? __($kunci) : (self::KATEGORI[$this->kategori] ?? $this->kategori);
    }

    public function ikon(): string
    {
        return self::IKON[$this->kategori] ?? 'fa-file';
    }

    // ── Video ────────────────────────────────────────────────────────

    /** Thumbnail resmi YouTube; hqdefault selalu tersedia untuk video publik. */
    public function thumbnail(): ?string
    {
        return $this->youtube_id ? 'https://i.ytimg.com/vi/' . $this->youtube_id . '/hqdefault.jpg' : null;
    }

    /**
     * Pemutar versi youtube-nocookie: YouTube tidak menaruh cookie pelacak
     * sampai pengunjung benar-benar memutar video.
     */
    public function embedUrl(): ?string
    {
        return $this->youtube_id ? 'https://www.youtube-nocookie.com/embed/' . $this->youtube_id . '?autoplay=1&rel=0' : null;
    }

    public function watchUrl(): ?string
    {
        return $this->youtube_id ? 'https://www.youtube.com/watch?v=' . $this->youtube_id : null;
    }

    /**
     * Ambil ID video dari URL YouTube apa pun bentuknya, atau dari ID mentah.
     * Mengembalikan null bila tidak dikenali.
     */
    public static function parseYoutubeId(?string $input): ?string
    {
        $input = trim((string) $input);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
            return $input;
        }

        $pola = '~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i';

        return preg_match($pola, $input, $m) ? $m[1] : null;
    }

    // ── Gambar (infografis / sampul materi) ───────────────────────

    /**
     * URL relatif ke /storage, bukan Storage::url(): yang terakhir menempelkan
     * APP_URL, yang sering tidak sama dengan domain tempat situs dibuka.
     */
    public function gambarUrl(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    public function hapusGambar(): void
    {
        if ($this->gambar) {
            Storage::disk('public')->delete($this->gambar);
        }
    }

    public function presentasiUrl(): ?string
    {
        return $this->presentasi ? asset('storage/' . $this->presentasi) : null;
    }

    public function hapusPresentasi(): void
    {
        if ($this->presentasi) {
            Storage::disk('public')->delete($this->presentasi);
        }
    }

    // ── Scope ────────────────────────────────────────────────────────

    /** Hanya konten yang ditandai tampil, dalam urutan publik. */
    public function scopePublik(Builder $query): Builder
    {
        return $query->where('tampil', true)->orderBy('urutan')->latest('id');
    }

    /** Hanya kategori yang dikenal — melindungi query dari slug asal-asalan. */
    public function scopeKategori(Builder $query, ?string $kategori): Builder
    {
        if ($kategori && array_key_exists($kategori, self::KATEGORI)) {
            $query->where('kategori', $kategori);
        }

        return $query;
    }
}
