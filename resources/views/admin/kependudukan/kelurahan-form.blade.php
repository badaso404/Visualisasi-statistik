@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Penduduk Kelurahan' : 'Tambah Penduduk Kelurahan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.penduduk-kelurahan.update', $item) : route('admin.penduduk-kelurahan.store') }}">
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
                <label class="form-label">Nama Kelurahan</label>
                <input type="text" name="nama_kelurahan" value="{{ old('nama_kelurahan', $item->nama_kelurahan) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Penduduk</label>
                <input type="number" name="jumlah_penduduk" value="{{ old('jumlah_penduduk', $item->jumlah_penduduk) }}" class="form-control" required>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Latitude <span class="text-muted">(opsional)</span></label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $item->latitude) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude <span class="text-muted">(opsional)</span></label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $item->longitude) }}" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kependudukan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
