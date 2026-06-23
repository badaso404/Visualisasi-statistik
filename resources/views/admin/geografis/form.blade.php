@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Data Geografis' : 'Tambah Data Geografis')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.geografis.update', $item) : route('admin.geografis.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Luas Kota (km²)</label>
                <input type="number" step="0.01" name="luas_kota_km2" value="{{ old('luas_kota_km2', $item->luas_kota_km2) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ketinggian (mdpl)</label>
                <input type="number" name="ketinggian_mdpl" value="{{ old('ketinggian_mdpl', $item->ketinggian_mdpl) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.geografis.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
