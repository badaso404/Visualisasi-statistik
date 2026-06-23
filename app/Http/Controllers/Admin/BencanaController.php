<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataBencana;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class BencanaController extends Controller
{
    public function index()
    {
        $items = DataBencana::with('kecamatan')
            ->orderByDesc('tahun')
            ->orderByDesc('tanggal_kejadian')
            ->get();

        return view('admin.bencana.index', compact('items'));
    }

    public function create()
    {
        return view('admin.bencana.form', [
            'item'      => new DataBencana(),
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
            'jenisList' => DataBencana::JENIS,
        ]);
    }

    public function store(Request $request)
    {
        DataBencana::create($this->validated($request));

        return redirect()->route('admin.bencana.index')->with('success', 'Data bencana ditambahkan.');
    }

    public function edit(DataBencana $bencana)
    {
        return view('admin.bencana.form', [
            'item'      => $bencana,
            'kecamatan' => Kecamatan::orderBy('nama_kecamatan')->get(),
            'jenisList' => DataBencana::JENIS,
        ]);
    }

    public function update(Request $request, DataBencana $bencana)
    {
        $bencana->update($this->validated($request));

        return redirect()->route('admin.bencana.index')->with('success', 'Data bencana diperbarui.');
    }

    public function destroy(DataBencana $bencana)
    {
        $bencana->delete();

        return redirect()->route('admin.bencana.index')->with('success', 'Data bencana dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kecamatan_id'     => ['nullable', 'exists:kecamatan,id'],
            'jenis_bencana'    => ['required', 'string', 'max:255'],
            'nama_lokasi'      => ['required', 'string', 'max:255'],
            'tahun'            => ['required', 'integer', 'min:1900', 'max:2100'],
            'tanggal_kejadian' => ['nullable', 'date'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'jumlah_kejadian'  => ['required', 'integer', 'min:0'],
            'jumlah_korban'    => ['required', 'integer', 'min:0'],
            'jumlah_terdampak' => ['required', 'integer', 'min:0'],
            'keterangan'       => ['nullable', 'string'],
            'sumber'           => ['nullable', 'string', 'max:255'],
        ]);
    }
}
