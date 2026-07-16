@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Kesehatan' : 'Tambah Kesehatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.kesehatan.update', $item) : route('admin.kesehatan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Tempat Tidur RS</label>
                <input type="number" name="jumlah_tempat_tidur_rs" value="{{ old('jumlah_tempat_tidur_rs', $item->jumlah_tempat_tidur_rs) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cakupan Imunisasi Dasar (%) <span class="text-muted">(opsional — tak tersedia di BPS)</span></label>
                <input type="number" step="0.01" name="cakupan_imunisasi_dasar" value="{{ old('cakupan_imunisasi_dasar', $item->cakupan_imunisasi_dasar) }}" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kesehatan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
