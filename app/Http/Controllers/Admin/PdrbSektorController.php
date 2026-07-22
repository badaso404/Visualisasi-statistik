<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CsvPerPeriode;
use App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk;
use App\Http\Controllers\Admin\Concerns\ValidasiPeriodeUnik;
use App\Http\Controllers\Controller;
use App\Models\PdrbSektor;
use Illuminate\Http\Request;

/**
 * Rincian PDRB per lapangan usaha. Tanpa alat isi-massal seperti modul
 * per-kecamatan: barisnya terikat 17 kategori baku BPS, bukan daftar wilayah
 * yang bisa bertambah — untuk koreksi banyak baris sekaligus dipakai CSV.
 */
class PdrbSektorController extends Controller
{
    use CsvPerPeriode;
    use ValidasiPeriodeUnik;
    use TahunMengikutiInduk;

    protected function tabelInduk(): ?string
    {
        return 'data_perekonomian';
    }

    protected function sebutanInduk(): string
    {
        return 'ringkasan PDRB';
    }

    protected function tabInduk(): string
    {
        return 'tab Ringkasan Tahunan';
    }

    /* ── CSV: satu baris per (tahun, kode_sektor) ─────────────────────── */

    protected function csvNama(): string
    {
        return 'pdrb-sektor';
    }

    protected function csvModel(): string
    {
        return PdrbSektor::class;
    }

    protected function csvKunci(): array
    {
        return ['tahun', 'kode_sektor'];
    }

    protected function csvKolom(): array
    {
        return [
            'kategori'         => 'teks',
            'nama_sektor'      => 'teks',
            'adhb'             => 'desimal',
            'distribusi'       => 'desimal',
            'laju_pertumbuhan' => 'desimal',
        ];
    }

    /**
     * BPS membakukan 17 lapangan usaha; di luar itu pasti salah ketik. Tahunnya
     * juga harus sudah punya ringkasan PDRB — satu tahun berisi 17 baris, jadi
     * salah tahun berarti 17 baris yang tidak akan tampil di situs publik.
     */
    protected function csvKunciValid(array $kunci): ?string
    {
        if ($kunci['kode_sektor'] < 1 || $kunci['kode_sektor'] > 17) {
            return "kode_sektor '{$kunci['kode_sektor']}' di luar 1-17";
        }

        return $this->tahunPunyaInduk($kunci['tahun'])
            ? null
            : "belum ada {$this->sebutanInduk()} tahun {$kunci['tahun']}";
    }

    /** `kategori` nullable; sisanya NOT NULL sehingga wajib saat baris dibuat. */
    protected function csvKolomWajib(): array
    {
        return ['nama_sektor', 'adhb', 'distribusi', 'laju_pertumbuhan'];
    }

    protected function csvContoh(): array
    {
        return [
            'kode_sektor'      => 7,
            'kategori'         => 'G',
            'nama_sektor'      => 'Perdagangan Besar dan Eceran; Reparasi Mobil dan Sepeda Motor',
            'adhb'             => 123886000,
            'distribusi'       => 19.73,
            'laju_pertumbuhan' => 6.63,
        ];
    }

    protected function csvRedirect(): string
    {
        return 'admin.perekonomian.index';
    }

    public function store(Request $request)
    {
        PdrbSektor::create($this->validated($request));

        return redirect()->route('admin.perekonomian.index')->with('success', 'Data lapangan usaha ditambahkan.');
    }

    public function update(Request $request, PdrbSektor $pdrbSektor)
    {
        $pdrbSektor->update($this->validated($request, $pdrbSektor));

        return redirect()->route('admin.perekonomian.index')->with('success', 'Data lapangan usaha diperbarui.');
    }

    public function destroy(PdrbSektor $pdrbSektor)
    {
        $pdrbSektor->delete();

        return redirect()->route('admin.perekonomian.index')->with('success', 'Data lapangan usaha dihapus.');
    }

    private function validated(Request $request, ?\Illuminate\Database\Eloquent\Model $item = null): array
    {
        return $request->validate([
            'tahun'            => $this->aturanTahunInduk(),
            // Kunci uniknya (tahun, kode_sektor); aturan dipasang di kode_sektor
            // agar pesan galat muncul di kolom yang membedakan baris.
            'kode_sektor'      => ['required', 'integer', 'between:1,17',
                $this->unikPerPeriode('pdrb_sektor', ['tahun' => $request->input('tahun')], $item),
            ],
            'kategori'         => ['nullable', 'string', 'max:12'],
            'nama_sektor'      => ['required', 'string', 'max:255'],
            'adhb'             => ['required', 'numeric', 'min:0'],
            'distribusi'       => ['required', 'numeric', 'between:0,100'],
            'laju_pertumbuhan' => ['required', 'numeric', 'between:-100,100'],
        ], [
            'kode_sektor.unique' => 'Lapangan usaha ini sudah ada pada tahun tersebut. Silakan edit baris yang sudah ada.',
        ] + $this->pesanTahunInduk());
    }
}
