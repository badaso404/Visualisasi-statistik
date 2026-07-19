@extends('admin.layout.app')
@section('title', 'Data Iklim')

@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Data Iklim (per bulan)</h5>
    <button class="btn btn-primary btn-sm"
            data-modal-form="#modalIklim"
            data-action="{{ route('admin.iklim.store') }}"
            data-title="Tambah Data Iklim">
        <i class="bi bi-plus-lg"></i> Tambah
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tahun</th><th>Bulan</th><th>Hari Hujan</th><th>Tekanan</th><th>Suhu</th>
                    <th>Angin</th><th>Kelembaban</th><th>Penyinaran</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ $namaBulan[$item->bulan] ?? $item->bulan }}</td>
                        <td>{{ $item->hari_hujan }}</td>
                        <td>{{ $item->tekanan_udara }}</td>
                        <td>{{ $item->suhu_udara }}</td>
                        <td>{{ $item->kecepatan_angin }}</td>
                        <td>{{ $item->kelembaban_udara }}</td>
                        <td>{{ $item->penyinaran_matahari }}</td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="#modalIklim"
                                    data-action="{{ route('admin.iklim.update', $item) }}"
                                    data-method="PUT"
                                    data-title="Edit Iklim {{ $namaBulan[$item->bulan] ?? '' }} {{ $item->tahun }}"
                                    data-fields="{{ json_encode($item->only(['tahun', 'bulan', 'hari_hujan', 'tekanan_udara', 'suhu_udara', 'kecepatan_angin', 'kelembaban_udara', 'penyinaran_matahari', 'sumber'])) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.iklim.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="data iklim {{ $namaBulan[$item->bulan] ?? $item->bulan }} {{ $item->tahun }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-admin.modal-form id="modalIklim" title="Tambah Data Iklim" :action="route('admin.iklim.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ($namaBulan as $n => $label)
                    <option value="{{ $n }}" @selected(old('bulan') == $n)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Hari Hujan</label>
            <input type="number" step="0.01" name="hari_hujan" value="{{ old('hari_hujan') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tekanan Udara</label>
            <input type="number" step="0.01" name="tekanan_udara" value="{{ old('tekanan_udara') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Suhu Udara (°C)</label>
            <input type="number" step="0.01" name="suhu_udara" value="{{ old('suhu_udara') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Kecepatan Angin</label>
            <input type="number" step="0.01" name="kecepatan_angin" value="{{ old('kecepatan_angin') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Kelembaban Udara (%)</label>
            <input type="number" step="0.01" name="kelembaban_udara" value="{{ old('kelembaban_udara') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Penyinaran Matahari (%)</label>
            <input type="number" step="0.01" name="penyinaran_matahari" value="{{ old('penyinaran_matahari') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
            <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>
@endsection
