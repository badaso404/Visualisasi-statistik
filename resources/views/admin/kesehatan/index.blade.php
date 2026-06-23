@extends('admin.layout.app')
@section('title', 'Kesehatan')

@section('content')
{{-- Ringkasan --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Kesehatan (ringkasan per tahun)</h5>
    <a href="{{ route('admin.kesehatan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Tahun</th><th>Tempat Tidur RS</th><th>Cakupan Imunisasi (%)</th><th>Sumber</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ number_format($item->jumlah_tempat_tidur_rs, 0, ',', '.') }}</td>
                        <td>{{ $item->cakupan_imunisasi_dasar }}</td>
                        <td>{{ $item->sumber ?: '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.kesehatan.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.kesehatan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tenaga Kesehatan --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Tenaga Kesehatan per Kecamatan</h6>
    <a href="{{ route('admin.tenaga-kesehatan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun</th><th>Total</th><th>Dokter</th><th>Perawat</th><th>Bidan</th><th>Ahli Gizi</th><th>Farmasi</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($tenaga as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ $row->jumlah_total }}</td>
                        <td>{{ $row->dokter }}</td>
                        <td>{{ $row->perawat }}</td>
                        <td>{{ $row->bidan }}</td>
                        <td>{{ $row->ahli_gizi }}</td>
                        <td>{{ $row->farmasi }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.tenaga-kesehatan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.tenaga-kesehatan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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

{{-- Fasilitas Kesehatan --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Fasilitas Kesehatan per Kecamatan</h6>
    <a href="{{ route('admin.fasilitas-kesehatan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun</th><th>Total</th><th>Klinik</th><th>Posyandu</th><th>Puskesmas</th><th>RS</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($fasilitas as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ $row->jumlah_total }}</td>
                        <td>{{ $row->klinik_kesehatan }}</td>
                        <td>{{ $row->posyandu }}</td>
                        <td>{{ $row->puskesmas }}</td>
                        <td>{{ $row->rumah_sakit }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.fasilitas-kesehatan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.fasilitas-kesehatan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
