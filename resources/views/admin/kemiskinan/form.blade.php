@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Kemiskinan' : 'Tambah Kemiskinan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.kemiskinan.update', $item) : route('admin.kemiskinan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah Penduduk Miskin <span class="text-muted">(jiwa)</span></label>
                    <input type="number" name="jumlah_penduduk_miskin" value="{{ old('jumlah_penduduk_miskin', $item->jumlah_penduduk_miskin) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Persentase Penduduk Miskin (%)</label>
                    <input type="number" step="0.01" name="persentase_penduduk_miskin" value="{{ old('persentase_penduduk_miskin', $item->persentase_penduduk_miskin) }}" class="form-control" min="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Garis Kemiskinan <span class="text-muted">(Rp per kapita/bulan)</span></label>
                    <input type="number" step="0.01" name="garis_kemiskinan" value="{{ old('garis_kemiskinan', $item->garis_kemiskinan) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Indeks Kedalaman (P1)</label>
                    <input type="number" step="0.01" name="indeks_kedalaman" value="{{ old('indeks_kedalaman', $item->indeks_kedalaman) }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Indeks Keparahan (P2)</label>
                    <input type="number" step="0.01" name="indeks_keparahan" value="{{ old('indeks_keparahan', $item->indeks_keparahan) }}" class="form-control" min="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kemiskinan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
