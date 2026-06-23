@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Fasilitas Kesehatan' : 'Tambah Fasilitas Kesehatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.fasilitas-kesehatan.update', $item) : route('admin.fasilitas-kesehatan.store') }}">
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
                    <label class="form-label">Klinik Kesehatan</label>
                    <input type="number" name="klinik_kesehatan" value="{{ old('klinik_kesehatan', $item->klinik_kesehatan ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Posyandu</label>
                    <input type="number" name="posyandu" value="{{ old('posyandu', $item->posyandu ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Puskesmas</label>
                    <input type="number" name="puskesmas" value="{{ old('puskesmas', $item->puskesmas ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rumah Sakit</label>
                    <input type="number" name="rumah_sakit" value="{{ old('rumah_sakit', $item->rumah_sakit ?? 0) }}" class="form-control" required>
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
