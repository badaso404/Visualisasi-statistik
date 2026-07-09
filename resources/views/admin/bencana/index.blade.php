@extends('admin.layout.app')
@section('title', 'Kebencanaan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0">Data Bencana</h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <a href="{{ route('admin.bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importBox"><i class="bi bi-upload"></i> Import CSV</button>
        <a href="{{ route('admin.bencana.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>
</div>

<div class="collapse mb-3" id="importBox">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.bencana.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row align-items-md-end gap-2">
                @csrf
                <div class="flex-grow-1">
                    <label class="form-label small mb-1">Pilih file CSV</label>
                    <input type="file" name="file" accept=".csv,text/csv" class="form-control form-control-sm" required>
                    <small class="text-muted">Kolom: tanggal_kejadian, jenis_bencana, nama_lokasi, kecamatan, tahun, latitude, longitude, jumlah_kejadian, jumlah_korban, jumlah_terdampak, keterangan, sumber. Baris dicocokkan (jenis + lokasi + tahun + tanggal) — bila sudah ada akan diperbarui.</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.bencana.template') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-arrow-down"></i> Template</a>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-upload"></i> Unggah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Jenis</th><th>Lokasi</th><th>Kecamatan</th><th>Tahun</th>
                    <th>Kejadian</th><th>Korban</th><th>Terdampak</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tanggal_kejadian ? \Carbon\Carbon::parse($item->tanggal_kejadian)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->jenis_bencana }}</td>
                        <td>{{ $item->nama_lokasi }}</td>
                        <td>{{ $item->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ number_format($item->jumlah_kejadian) }}</td>
                        <td>{{ number_format($item->jumlah_korban) }}</td>
                        <td>{{ number_format($item->jumlah_terdampak) }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.bencana.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.bencana.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
