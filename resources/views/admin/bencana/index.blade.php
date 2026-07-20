@extends('admin.layout.app')
@section('title', 'Kebencanaan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="mb-0">Rekap Bencana Triwulanan</h5>
        <small class="text-muted">Jakarta Barat &middot; sumber: API Satu Data Jakarta</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <form action="{{ route('admin.bencana.sync') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-info btn-sm"><i class="bi bi-arrow-repeat"></i> Sync dari API</button>
        </form>
        <a href="{{ route('admin.bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importBox"><i class="bi bi-upload"></i> Import CSV</button>
        <button class="btn btn-primary btn-sm"
                data-modal-form="#modalBencana"
                data-action="{{ route('admin.bencana.store') }}"
                data-title="Tambah Rekap Bencana">
            <i class="bi bi-plus-lg"></i> Tambah
        </button>
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
                    <small class="text-muted">Kolom: tahun, triwulan, jenis_bencana, jumlah_kejadian, jumlah_korban_meninggal, jumlah_korban_luka, keterangan, sumber. Baris dicocokkan (tahun + triwulan + jenis) — bila sudah ada akan diperbarui.</small>
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
                    <th>Periode</th><th>Triwulan</th><th>Jenis</th>
                    <th>Kejadian</th><th>Korban Meninggal</th><th>Korban Luka</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->periode_label }}</td>
                        <td>{{ $item->triwulan ? 'TW' . $item->triwulan : '-' }}</td>
                        <td>{{ $item->jenis_bencana }}</td>
                        <td>{{ number_format($item->jumlah_kejadian) }}</td>
                        <td>{{ number_format($item->jumlah_korban_meninggal) }}</td>
                        <td>{{ number_format($item->jumlah_korban_luka) }}</td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="#modalBencana"
                                    data-action="{{ route('admin.bencana.update', $item) }}"
                                    data-method="PUT"
                                    data-title="Edit {{ $item->jenis_bencana }} — {{ $item->periode_label }}"
                                    data-fields="{{ json_encode($item->only(['jenis_bencana', 'tahun', 'triwulan', 'jumlah_kejadian', 'jumlah_korban_meninggal', 'jumlah_korban_luka', 'keterangan', 'sumber'])) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.bencana.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="{{ $item->jenis_bencana }} {{ $item->periode_label }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data rekap. Klik "Sync dari API" untuk menarik data dari Satu Data Jakarta.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-admin.modal-form id="modalBencana" title="Tambah Rekap Bencana" :action="route('admin.bencana.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-12">
            <div class="alert alert-light border small mb-0">
                Data ini berupa <strong>rekap triwulanan Jakarta Barat</strong> (mengikuti granularitas API Satu Data Jakarta),
                bukan catatan kejadian per lokasi. Karena itu tidak ada isian lokasi, kecamatan, maupun tanggal.
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" class="form-control" min="2000" max="2100" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Triwulan</label>
            <select name="triwulan" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ([1 => 'TW1 (Jan–Mar)', 2 => 'TW2 (Apr–Jun)', 3 => 'TW3 (Jul–Sep)', 4 => 'TW4 (Okt–Des)'] as $tw => $label)
                    <option value="{{ $tw }}" @selected(old('triwulan') == $tw)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jenis Bencana</label>
            <select name="jenis_bencana" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ($jenisList as $jenis)
                    <option value="{{ $jenis }}" @selected(old('jenis_bencana') == $jenis)>{{ $jenis }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Kejadian</label>
            <input type="number" name="jumlah_kejadian" value="{{ old('jumlah_kejadian', 0) }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Korban Meninggal</label>
            <input type="number" name="jumlah_korban_meninggal" value="{{ old('jumlah_korban_meninggal', 0) }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Korban Luka-luka</label>
            <input type="number" name="jumlah_korban_luka" value="{{ old('jumlah_korban_luka', 0) }}" class="form-control" min="0" required>
        </div>
        <div class="col-12">
            <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
            <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
            <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>
@endsection
