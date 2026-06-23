@extends('admin.layout.app')
@section('title', 'Kependudukan')

@section('content')
{{-- Ringkasan --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Kependudukan (ringkasan per tahun)</h5>
    <a href="{{ route('admin.kependudukan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Tahun</th><th>Laki-laki</th><th>Perempuan</th><th>Total</th><th>Sumber</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ number_format($item->jumlah_laki_laki, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->jumlah_perempuan, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->jumlah_total, 0, ',', '.') }}</td>
                        <td>{{ $item->sumber ?: '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.kependudukan.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.kependudukan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Per Kecamatan --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Penduduk per Kecamatan</h6>
    <a href="{{ route('admin.penduduk-kecamatan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun</th><th>Jumlah Penduduk</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($perKecamatan as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ number_format($row->jumlah_penduduk, 0, ',', '.') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.penduduk-kecamatan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.penduduk-kecamatan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Per Kelurahan --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Penduduk per Kelurahan</h6>
    <a href="{{ route('admin.penduduk-kelurahan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kelurahan</th><th>Kecamatan</th><th>Tahun</th><th>Penduduk</th><th>Lat</th><th>Long</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($perKelurahan as $row)
                    <tr>
                        <td>{{ $row->nama_kelurahan }}</td>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ number_format($row->jumlah_penduduk, 0, ',', '.') }}</td>
                        <td>{{ $row->latitude ?: '-' }}</td>
                        <td>{{ $row->longitude ?: '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.penduduk-kelurahan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.penduduk-kelurahan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
