<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KecamatanController extends Controller
{
    public function index()
    {
        $items = Kecamatan::orderBy('nama_kecamatan')->get();

        // Ditampilkan di tabel supaya admin tahu bobot sebuah kecamatan
        // sebelum menekan tombol hapus.
        $rincian = $items->mapWithKeys(fn (Kecamatan $k) => [
            $k->id => [
                'terhapus' => $k->rincianIkutTerhapus(),
                'lepas'    => $k->rincianKehilanganKaitan(),
            ],
        ]);

        return view('admin.kecamatan.index', compact('items', 'rincian'));
    }

    public function store(Request $request)
    {
        Kecamatan::create($this->validated($request));

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan ditambahkan.');
    }

    public function update(Request $request, Kecamatan $kecamatan)
    {
        $kecamatan->update($this->validated($request, $kecamatan));

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan diperbarui.');
    }

    /**
     * Foreign key ke kecamatan memakai cascadeOnDelete, jadi menghapus satu
     * kecamatan diam-diam melenyapkan data 9 modul untuk semua tahun. Karena
     * tidak bisa dibatalkan, penghapusan ditolak selama datanya masih ada —
     * admin harus mengosongkannya lebih dulu secara sadar.
     */
    public function destroy(Kecamatan $kecamatan)
    {
        $rincian = $kecamatan->rincianIkutTerhapus();

        if ($rincian !== []) {
            $daftar = collect($rincian)
                ->map(fn (int $jumlah, string $label) => "{$label} ({$jumlah})")
                ->implode(', ');

            return redirect()->route('admin.kecamatan.index')->with(
                'error',
                "Kecamatan {$kecamatan->nama_kecamatan} tidak bisa dihapus karena masih dipakai " .
                array_sum($rincian) . " baris data: {$daftar}. Hapus data tersebut lebih dulu bila memang ingin melanjutkan."
            );
        }

        $kecamatan->delete();

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan dihapus.');
    }

    private function validated(Request $request, ?Kecamatan $item = null): array
    {
        return $request->validate([
            'nama_kecamatan' => [
                'required', 'string', 'max:255',
                Rule::unique('kecamatan', 'nama_kecamatan')->ignore($item?->getKey()),
            ],
        ], [
            'nama_kecamatan.unique' => 'Kecamatan dengan nama ini sudah ada.',
        ]);
    }
}
