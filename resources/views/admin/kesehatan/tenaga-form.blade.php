@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Tenaga Kesehatan' : 'Tambah Tenaga Kesehatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.tenaga-kesehatan.update', $item) : route('admin.tenaga-kesehatan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" class="form-select" required>
                        <option value="">— pilih —</option>
                        @foreach ($kecamatan as $k)
                            <option value="{{ $k->id }}" @selected(old('kecamatan_id', $item->kecamatan_id) == $k->id)>{{ $k->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Total</label>
                    <input type="number" name="jumlah_total" value="{{ old('jumlah_total', $item->jumlah_total) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dokter</label>
                    <input type="number" name="dokter" value="{{ old('dokter', $item->dokter ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Perawat</label>
                    <input type="number" name="perawat" value="{{ old('perawat', $item->perawat ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bidan</label>
                    <input type="number" name="bidan" value="{{ old('bidan', $item->bidan ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ahli Gizi</label>
                    <input type="number" name="ahli_gizi" value="{{ old('ahli_gizi', $item->ahli_gizi ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Farmasi</label>
                    <input type="number" name="farmasi" value="{{ old('farmasi', $item->farmasi ?? 0) }}" class="form-control" required>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kesehatan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
