<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Form "isi massal": satu tahun, semua kecamatan sekaligus, satu tombol simpan.
 *
 * Menggantikan alur lama yang mengharuskan admin membuka form tambah
 * sebanyak jumlah kecamatan untuk satu tahun data yang sama.
 *
 * Controller pemakai wajib menyediakan: batchModel(), batchFields(),
 * batchJudul(), batchRoutePrefix(), dan batchRedirect().
 */
trait IsiMassalPerKecamatan
{
    /** @return class-string<\Illuminate\Database\Eloquent\Model> */
    abstract protected function batchModel(): string;

    /**
     * Kolom numerik yang diisi per kecamatan.
     *
     * Bentuk sederhana (bilangan bulat):  ['jumlah_pelajar' => 'Pelajar']
     * Kolom desimal:                      ['persentase' => ['label' => 'Persentase (%)', 'desimal' => true]]
     * Kolom dengan batas khusus (mis. smallint): ['jumlah_rw' => ['label' => 'RW', 'maks' => 65535]]
     */
    abstract protected function batchFields(): array;

    /** Normalisasi batchFields() jadi ['nama' => ['label', 'desimal', 'maks']]. */
    private function batchFieldsNormal(): array
    {
        $hasil = [];

        foreach ($this->batchFields() as $nama => $def) {
            $hasil[$nama] = is_array($def)
                ? ['label' => $def['label'], 'desimal' => (bool) ($def['desimal'] ?? false), 'maks' => $def['maks'] ?? null]
                : ['label' => $def, 'desimal' => false, 'maks' => null];
        }

        return $hasil;
    }

    /** Judul halaman form isi massal. */
    abstract protected function batchJudul(): string;

    /** Awalan nama route, mis. 'admin.pendidikan-kecamatan'. */
    abstract protected function batchRoutePrefix(): string;

    /** Nama route tujuan setelah simpan. */
    abstract protected function batchRedirect(): string;

    /**
     * Pemeriksaan lintas-baris spesifik modul sebelum data batch disimpan.
     * Kembalikan pesan error untuk membatalkan penyimpanan, atau null bila lolos.
     *
     * @param array<int|string, array<string, mixed>> $data  kecamatan_id => [field => nilai]
     */
    protected function batchPeriksaTambahan(int $tahun, array $data): ?string
    {
        return null;
    }

    /*
     * Tiga seam di bawah memisahkan "tahun yang dipilih operator" dari "cara baris
     * disimpan". Default-nya berbasis kolom `tahun` (dipakai semua modul anak
     * kecuali luas_kecamatan, yang menyimpan data_geografis_id sebagai foreign
     * key sehingga meng-override ketiganya). Perilaku modul lain tak berubah.
     */

    /** Baris anak tahun terpilih, dikunci per kecamatan_id untuk mengisi form. */
    protected function batchExisting(int $tahun): \Illuminate\Support\Collection
    {
        return $this->batchModel()::where('tahun', $tahun)->get()->keyBy('kecamatan_id');
    }

    /** Tahun-tahun yang sudah punya data anak, terbaru dulu. */
    protected function batchTahunAda(): \Illuminate\Support\Collection
    {
        return $this->batchModel()::distinct()->orderByDesc('tahun')->pluck('tahun');
    }

    /** Kolom kunci updateOrCreate untuk satu baris (kecamatan pada tahun tsb). */
    protected function batchKunciSimpan(int $tahun, $kecamatanId): array
    {
        return ['kecamatan_id' => $kecamatanId, 'tahun' => $tahun];
    }

    public function batch(Request $request)
    {
        $model = $this->batchModel();

        // Untuk modul yang terikat induk, tahun bawaannya adalah tahun ringkasan
        // TERBARU, bukan tahun berjalan: now()->year kerap belum punya ringkasan
        // sehingga form langsung terbuka pada pilihan yang tidak bisa disimpan.
        $tahunInduk = $this->tahunInduk();
        $bawaan     = $this->terikatInduk()
            ? (int) ($tahunInduk->first() ?: now()->year)
            : now()->year;

        $tahun = (int) ($request->query('tahun') ?: $bawaan);

        return view('admin.partials.isi-massal', [
            'judul'        => $this->batchJudul(),
            'tahun'        => $tahun,
            'fields'       => $this->batchFieldsNormal(),
            'kecamatan'    => Kecamatan::orderBy('nama_kecamatan')->get(),
            'existing'     => $this->batchExisting($tahun),
            'tahunAda'     => $this->batchTahunAda(),
            // Kosong = modul tidak terikat induk, view menampilkan input bebas.
            'tahunInduk'   => $tahunInduk,
            'sebutanInduk' => $this->terikatInduk() ? $this->sebutanInduk() : null,
            'routeBatch'   => $this->batchRoutePrefix() . '.batch',
            'routeSimpan'  => $this->batchRoutePrefix() . '.batch.store',
            'routeKembali' => $this->batchRedirect(),
        ]);
    }

    public function batchStore(Request $request)
    {
        $model  = $this->batchModel();
        $defs   = $this->batchFieldsNormal();
        $fields = array_keys($defs);

        $rules = [
            'tahun'  => $this->aturanTahunInduk(),
            'data'   => ['required', 'array'],
            'data.*' => ['array'],
        ];
        // Pesan ramah: tanpa batas 'max', angka > 2,1 M lolos validasi lalu
        // ditolak MySQL dengan "Out of range" (layar merah). Batas ini
        // mengubahnya jadi pesan yang bisa dibaca operator. 2147483647 = batas
        // atas kolom INT bertanda MySQL.
        $maksInt  = 2147483647;
        $messages = $this->pesanTahunInduk();
        foreach ($defs as $field => $def) {
            // Batas ikut tipe kolom: kolom smallint (mis. RW/kelurahan) memberi
            // 'maks' sendiri; selain itu memakai batas INT bertanda.
            $maks = $def['maks'] ?? $maksInt;

            $rules["data.*.{$field}"] = $def['desimal']
                ? ['nullable', 'numeric', 'min:0']
                : ['nullable', 'integer', 'min:0', 'max:' . $maks];

            $label = $def['label'];
            $messages["data.*.{$field}.max"]     = "Nilai {$label} terlalu besar (maksimum " . number_format($maks, 0, ',', '.') . ').';
            $messages["data.*.{$field}.integer"] = "Nilai {$label} harus berupa bilangan bulat.";
            $messages["data.*.{$field}.numeric"] = "Nilai {$label} harus berupa angka.";
            $messages["data.*.{$field}.min"]     = "Nilai {$label} tidak boleh negatif.";
        }

        // Isi massal menulis satu tahun untuk SEMUA kecamatan sekaligus, jadi
        // salah tahun di sini menghasilkan delapan baris yang tak terlihat di
        // situs publik — bukan satu. Karena itu ikatan induknya ikut diperiksa.
        $validated = $request->validate($rules, $messages);

        // Kesempatan modul memeriksa keseluruhan (mis. total anak tak boleh
        // melebihi ringkasan induk) sebelum satu baris pun ditulis.
        if ($pesan = $this->batchPeriksaTambahan((int) $validated['tahun'], $validated['data'])) {
            return back()->withInput()->with('error', $pesan);
        }

        // Satu tabel isian = satu satuan kerja. Kalau baris ke-5 gagal, empat
        // baris sebelumnya ikut dibatalkan supaya tidak tersimpan separuh.
        $tersimpan = DB::transaction(function () use ($model, $validated, $fields) {
            $jumlah = 0;

            foreach ($validated['data'] as $kecamatanId => $baris) {
                $nilai = [];
                foreach ($fields as $field) {
                    $isi = $baris[$field] ?? null;
                    if ($isi !== null && $isi !== '') {
                        $nilai[$field] = $isi;
                    }
                }

                // Baris yang seluruh kolomnya dikosongkan dianggap "belum ada data"
                // dan dilewati, bukan disimpan sebagai deretan nol.
                if ($nilai === []) {
                    continue;
                }

                $model::updateOrCreate(
                    $this->batchKunciSimpan((int) $validated['tahun'], $kecamatanId),
                    $nilai,
                );
                $jumlah++;
            }

            return $jumlah;
        });

        return redirect()->route($this->batchRedirect())
            ->with('success', "{$tersimpan} baris data kecamatan tahun {$validated['tahun']} disimpan.");
    }
}
