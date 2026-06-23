@extends('admin.layout.app')
@section('title', 'Data Iklim')

@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Data Iklim (per bulan)</h5>
    <a href="{{ route('admin.iklim.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
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
                            <a href="{{ route('admin.iklim.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.iklim.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
@endsection
