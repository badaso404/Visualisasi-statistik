@extends('admin.layout.app')
@section('title', 'Data Geografis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Data Geografis (ringkasan)</h5>
    <a href="{{ route('admin.geografis.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Tahun</th><th>Luas Kota (km²)</th><th>Ketinggian (mdpl)</th><th>Sumber</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ $item->luas_kota_km2 }}</td>
                        <td>{{ $item->ketinggian_mdpl }}</td>
                        <td>{{ $item->sumber ?: '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.geografis.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.geografis.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Luas per Kecamatan</h6>
    <a href="{{ route('admin.luas-kecamatan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun Data</th><th>Luas (km²)</th><th>Persentase (%)</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($luas as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->dataGeografis->tahun ?? '-' }}</td>
                        <td>{{ $row->luas_km2 }}</td>
                        <td>{{ $row->persentase }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.luas-kecamatan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.luas-kecamatan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
@endsection
