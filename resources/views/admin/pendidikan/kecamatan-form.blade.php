@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Pendidikan Kecamatan' : 'Tambah Pendidikan Kecamatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.pendidikan-kecamatan.update', $item) : route('admin.pendidikan-kecamatan.store') }}">
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

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Pelajar</label>
                    <input type="number" name="jumlah_pelajar" value="{{ old('jumlah_pelajar', $item->jumlah_pelajar) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah Pendidik</label>
                    <input type="number" name="jumlah_pendidik" value="{{ old('jumlah_pendidik', $item->jumlah_pendidik) }}" class="form-control" min="0" required>
                </div>
            </div>

            <label class="form-label fw-semibold">Jumlah Sekolah</label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Negeri</label>
                    <input type="number" name="jumlah_sekolah_negeri" value="{{ old('jumlah_sekolah_negeri', $item->jumlah_sekolah_negeri) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Swasta</label>
                    <input type="number" name="jumlah_sekolah_swasta" value="{{ old('jumlah_sekolah_swasta', $item->jumlah_sekolah_swasta) }}" class="form-control" min="0" required>
                </div>
            </div>
            <div class="text-muted small mt-1">Total sekolah dihitung otomatis dari negeri + swasta.</div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.pendidikan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
