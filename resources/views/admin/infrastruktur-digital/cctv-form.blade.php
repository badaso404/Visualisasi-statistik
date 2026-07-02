@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit CCTV' : 'Tambah CCTV')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.cctv.update', $item) : route('admin.cctv.store') }}">
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
                    <label class="form-label">Jumlah Unit</label>
                    <input type="number" name="jumlah_unit" value="{{ old('jumlah_unit', $item->jumlah_unit ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit Aktif</label>
                    <input type="number" name="unit_aktif" value="{{ old('unit_aktif', $item->unit_aktif ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Terintegrasi</label>
                    <input type="number" name="terintegrasi" value="{{ old('terintegrasi', $item->terintegrasi ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $item->keterangan) }}" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.infrastruktur-digital.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
