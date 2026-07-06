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
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="mb-0">Penduduk per Kelurahan</h6>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.penduduk-kelurahan.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <a href="{{ route('admin.penduduk-kelurahan.template') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-arrow-down"></i> Template</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportKelurahan"><i class="bi bi-upload"></i> Import CSV</button>
        <a href="{{ route('admin.penduduk-kelurahan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>
</div>

{{-- Modal Import CSV --}}
<div class="modal fade" id="modalImportKelurahan" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.penduduk-kelurahan.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title">Import Data Kelurahan (CSV)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pilih file CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" class="form-control" required>
                </div>
                <div class="small text-muted">
                    Kolom: <code>kecamatan, tahun, nama_kelurahan, latitude, longitude, jumlah_penduduk</code>.<br>
                    Baris dicocokkan per <b>nama_kelurahan + tahun</b> — data yang sudah ada akan <b>diperbarui</b> (termasuk lat/lng), bukan diduplikat.
                    Belum punya format? <a href="{{ route('admin.penduduk-kelurahan.template') }}">Unduh template</a>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Import</button>
            </div>
        </form>
    </div>
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
