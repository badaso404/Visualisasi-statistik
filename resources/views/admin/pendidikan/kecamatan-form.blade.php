@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Pendidikan Kecamatan' : 'Tambah Pendidikan Kecamatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
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
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jumlah Murid</label>
                    <input type="number" name="jumlah_murid" value="{{ old('jumlah_murid', $item->jumlah_murid) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Guru</label>
                    <input type="number" name="jumlah_guru" value="{{ old('jumlah_guru', $item->jumlah_guru) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Sekolah</label>
                    <input type="number" name="jumlah_sekolah" value="{{ old('jumlah_sekolah', $item->jumlah_sekolah) }}" class="form-control" required>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.pendidikan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
