@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Luas Kecamatan' : 'Tambah Luas Kecamatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.luas-kecamatan.update', $item) : route('admin.luas-kecamatan.store') }}">
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
                <label class="form-label">Tahun Data Geografis</label>
                <select name="data_geografis_id" class="form-select" required>
                    <option value="">— pilih —</option>
                    @foreach ($geografis as $g)
                        <option value="{{ $g->id }}" @selected(old('data_geografis_id', $item->data_geografis_id) == $g->id)>{{ $g->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Luas (km²)</label>
                <input type="number" step="0.01" name="luas_km2" value="{{ old('luas_km2', $item->luas_km2) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Persentase (%)</label>
                <input type="number" step="0.01" name="persentase" value="{{ old('persentase', $item->persentase) }}" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.geografis.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
