@extends('admin.layout.app')
@section('title', 'Infrastruktur Digital')

@section('content')
{{-- JakWiFi --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">JakWiFi per Kecamatan</h5>
    <a href="{{ route('admin.jak-wifi.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun</th><th>Total Titik</th><th>Titik Aktif</th><th>Pengguna</th><th>Keterangan</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($jakWifi as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ number_format($row->jumlah_titik, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->titik_aktif, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->jumlah_pengguna, 0, ',', '.') }}</td>
                        <td>{{ $row->keterangan ?: '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.jak-wifi.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.jak-wifi.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CCTV --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">CCTV per Kecamatan</h6>
    <a href="{{ route('admin.cctv.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun</th><th>Total Unit</th><th>Unit Aktif</th><th>Terintegrasi</th><th>Keterangan</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($cctv as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ number_format($row->jumlah_unit, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->unit_aktif, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->terintegrasi, 0, ',', '.') }}</td>
                        <td>{{ $row->keterangan ?: '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.cctv.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.cctv.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
