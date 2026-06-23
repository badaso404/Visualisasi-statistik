@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Penduduk Kecamatan' : 'Tambah Penduduk Kecamatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.penduduk-kecamatan.update', $item) : route('admin.penduduk-kecamatan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Kecamatan</label>
                <select name="kecamatan_id" class="form-select" required>
                    <option value="">— pilih —</option>
                    @foreach ($kecamatan as $k)
                        <option value="{{ $k->id }}" @selected(old('kecamatan_id', $item->kecamatan_id) == $k->id)>{{ $k->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Penduduk</label>
                <input type="number" name="jumlah_penduduk" value="{{ old('jumlah_penduduk', $item->jumlah_penduduk) }}" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kependudukan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
