@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Kemiskinan Kecamatan' : 'Tambah Kemiskinan Kecamatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.kemiskinan-kecamatan.update', $item) : route('admin.kemiskinan-kecamatan.store') }}">
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
                    <label class="form-label">Penduduk Miskin <span class="text-muted">(jiwa)</span></label>
                    <input type="number" name="jumlah_penduduk_miskin" value="{{ old('jumlah_penduduk_miskin', $item->jumlah_penduduk_miskin) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keluarga Miskin <span class="text-muted">(KK)</span></label>
                    <input type="number" name="jumlah_keluarga_miskin" value="{{ old('jumlah_keluarga_miskin', $item->jumlah_keluarga_miskin) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penerima Bantuan <span class="text-muted">(jiwa)</span></label>
                    <input type="number" name="penerima_bantuan" value="{{ old('penerima_bantuan', $item->penerima_bantuan) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Persentase (%)</label>
                    <input type="number" step="0.01" name="persentase" value="{{ old('persentase', $item->persentase) }}" class="form-control" min="0" required>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kemiskinan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
