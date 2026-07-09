@extends('admin.layout.app')
@section('title', 'Titik Peta Bencana')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0">Titik Peta Bencana</h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <a href="{{ route('admin.titik-bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importBox"><i class="bi bi-upload"></i> Import CSV</button>
        <a href="{{ route('admin.titik-bencana.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>
</div>

<div class="collapse mb-3" id="importBox">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.titik-bencana.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row align-items-md-end gap-2">
                @csrf
                <div class="flex-grow-1">
                    <label class="form-label small mb-1">Pilih file CSV</label>
                    <input type="file" name="file" accept=".csv,text/csv" class="form-control form-control-sm" required>
                    <small class="text-muted">Kolom: kategori, level, nama, kecamatan, latitude, longitude, link_maps, keterangan. Kategori: <code>banjir_rawan</code> (level 1–3), <code>pos_damkar</code>, <code>zona_aman</code>. Baris dicocokkan (kategori + nama) — bila sudah ada akan diperbarui.</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.titik-bencana.template') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-arrow-down"></i> Template</a>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-upload"></i> Unggah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<p class="text-muted small">Titik referensi pada peta sebaran bencana: zona rawan banjir (Prioritas 1–3), pos Damkar, dan zona aman evakuasi.</p>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Kategori</th><th>Level</th><th>Nama</th><th>Kecamatan</th>
                    <th>Latitude</th><th>Longitude</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ \App\Models\TitikBencana::KATEGORI[$item->kategori] ?? $item->kategori }}</td>
                        <td>{{ $item->level ? 'Prioritas ' . $item->level : '-' }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $item->latitude }}</td>
                        <td>{{ $item->longitude }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.titik-bencana.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.titik-bencana.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus titik ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada titik.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
