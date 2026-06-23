@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Pendidikan' : 'Tambah Pendidikan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.pendidikan.update', $item) : route('admin.pendidikan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">APM SD/MI</label>
                    <input type="number" step="0.01" name="apm_sd_mi" value="{{ old('apm_sd_mi', $item->apm_sd_mi) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">APM SMP/MTs</label>
                    <input type="number" step="0.01" name="apm_smp_mts" value="{{ old('apm_smp_mts', $item->apm_smp_mts) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">APM SMA/SMK/MAN</label>
                    <input type="number" step="0.01" name="apm_sma_smk_man" value="{{ old('apm_sma_smk_man', $item->apm_sma_smk_man) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">APK SD/MI</label>
                    <input type="number" step="0.01" name="apk_sd_mi" value="{{ old('apk_sd_mi', $item->apk_sd_mi) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">APK SMP/MTs</label>
                    <input type="number" step="0.01" name="apk_smp_mts" value="{{ old('apk_smp_mts', $item->apk_smp_mts) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">APK SMA/SMK/MAN</label>
                    <input type="number" step="0.01" name="apk_sma_smk_man" value="{{ old('apk_sma_smk_man', $item->apk_sma_smk_man) }}" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
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
