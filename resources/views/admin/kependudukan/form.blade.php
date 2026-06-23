@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Kependudukan' : 'Tambah Kependudukan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.kependudukan.update', $item) : route('admin.kependudukan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Laki-laki</label>
                <input type="number" name="jumlah_laki_laki" value="{{ old('jumlah_laki_laki', $item->jumlah_laki_laki) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Perempuan</label>
                <input type="number" name="jumlah_perempuan" value="{{ old('jumlah_perempuan', $item->jumlah_perempuan) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Total</label>
                <input type="number" name="jumlah_total" value="{{ old('jumlah_total', $item->jumlah_total) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kependudukan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
