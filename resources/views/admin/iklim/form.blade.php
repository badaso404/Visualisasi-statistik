@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Data Iklim' : 'Tambah Data Iklim')

@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

@section('content')
<div class="card border-0 shadow-sm" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.iklim.update', $item) : route('admin.iklim.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $item->tahun) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select" required>
                        <option value="">— pilih —</option>
                        @foreach ($namaBulan as $n => $label)
                            <option value="{{ $n }}" @selected(old('bulan', $item->bulan) == $n)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hari Hujan</label>
                    <input type="number" step="0.01" name="hari_hujan" value="{{ old('hari_hujan', $item->hari_hujan) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tekanan Udara</label>
                    <input type="number" step="0.01" name="tekanan_udara" value="{{ old('tekanan_udara', $item->tekanan_udara) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Suhu Udara (°C)</label>
                    <input type="number" step="0.01" name="suhu_udara" value="{{ old('suhu_udara', $item->suhu_udara) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecepatan Angin</label>
                    <input type="number" step="0.01" name="kecepatan_angin" value="{{ old('kecepatan_angin', $item->kecepatan_angin) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelembaban Udara (%)</label>
                    <input type="number" step="0.01" name="kelembaban_udara" value="{{ old('kelembaban_udara', $item->kelembaban_udara) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penyinaran Matahari (%)</label>
                    <input type="number" step="0.01" name="penyinaran_matahari" value="{{ old('penyinaran_matahari', $item->penyinaran_matahari) }}" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.iklim.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
