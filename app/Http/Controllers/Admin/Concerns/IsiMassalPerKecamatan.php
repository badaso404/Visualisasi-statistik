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
     */
    abstract protected function batchFields(): array;

    /** Normalisasi batchFields() jadi ['nama' => ['label' => ..., 'desimal' => bool]]. */
    private function batchFieldsNormal(): array
    {
        $hasil = [];

        foreach ($this->batchFields() as $nama => $def) {
            $hasil[$nama] = is_array($def)
                ? ['label' => $def['label'], 'desimal' => (bool) ($def['desimal'] ?? false)]
                : ['label' => $def, 'desimal' => false];
        }

        return $hasil;
    }

    /** Judul halaman form isi massal. */
    abstract protected function batchJudul(): string;

    /** Awalan nama route, mis. 'admin.pendidikan-kecamatan'. */
    abstract protected function batchRoutePrefix(): string;

    /** Nama route tujuan setelah simpan. */
    abstract protected function batchRedirect(): string;

    public function batch(Request $request)
    {
        $model = $this->batchModel();
        $tahun = (int) ($request->query('tahun') ?: now()->year);

        return view('admin.partials.isi-massal', [
            'judul'        => $this->batchJudul(),
            'tahun'        => $tahun,
            'fields'       => $this->batchFieldsNormal(),
            'kecamatan'    => Kecamatan::orderBy('nama_kecamatan')->get(),
            'existing'     => $model::where('tahun', $tahun)->get()->keyBy('kecamatan_id'),
            'tahunAda'     => $model::distinct()->orderByDesc('tahun')->pluck('tahun'),
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
            'tahun'  => ['required', 'integer', 'min:1900', 'max:2100'],
            'data'   => ['required', 'array'],
            'data.*' => ['array'],
        ];
        foreach ($defs as $field => $def) {
            $rules["data.*.{$field}"] = ['nullable', $def['desimal'] ? 'numeric' : 'integer', 'min:0'];
        }

        $validated = $request->validate($rules);

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
                    ['kecamatan_id' => $kecamatanId, 'tahun' => $validated['tahun']],
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
