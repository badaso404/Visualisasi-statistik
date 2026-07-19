@extends('admin.layout.app')
@section('title', 'Kebencanaan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0">Data Bencana</h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <a href="{{ route('admin.bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importBox"><i class="bi bi-upload"></i> Import CSV</button>
        <button class="btn btn-primary btn-sm"
                data-modal-form="#modalBencana"
                data-action="{{ route('admin.bencana.store') }}"
                data-title="Tambah Data Bencana">
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
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="#modalBencana"
                                    data-action="{{ route('admin.bencana.update', $item) }}"
                                    data-method="PUT"
                                    data-title="Edit {{ $item->jenis_bencana }} — {{ $item->nama_lokasi }}"
                                    data-fields="{{ json_encode(array_merge($item->only(['jenis_bencana', 'kecamatan_id', 'nama_lokasi', 'tahun', 'latitude', 'longitude', 'jumlah_kejadian', 'jumlah_korban', 'jumlah_terdampak', 'keterangan', 'sumber']), ['tanggal_kejadian' => $item->tanggal_kejadian ? \Carbon\Carbon::parse($item->tanggal_kejadian)->format('Y-m-d') : null])) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.bencana.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="{{ $item->jenis_bencana }} di {{ $item->nama_lokasi }} ({{ $item->tahun }})">
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

<x-admin.modal-form id="modalBencana" title="Tambah Data Bencana" :action="route('admin.bencana.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Jenis Bencana</label>
            <select name="jenis_bencana" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ($jenisList as $jenis)
                    <option value="{{ $jenis }}" @selected(old('jenis_bencana') == $jenis)>{{ $jenis }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Kecamatan <span class="text-muted">(opsional)</span></label>
            <select name="kecamatan_id" class="form-select">
                <option value="">— pilih —</option>
                @foreach ($kecamatan as $k)
                    <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Nama Lokasi</label>
            <input type="text" name="nama_lokasi" value="{{ old('nama_lokasi') }}" class="form-control" placeholder="mis. Kel. Kapuk" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal Kejadian <span class="text-muted">(opsional)</span></label>
            <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Latitude <span class="text-muted">(opsional)</span></label>
            <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="-6.15">
        </div>
        <div class="col-md-4">
            <label class="form-label">Longitude <span class="text-muted">(opsional)</span></label>
            <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="106.78">
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Kejadian</label>
            <input type="number" name="jumlah_kejadian" value="{{ old('jumlah_kejadian', 1) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Korban</label>
            <input type="number" name="jumlah_korban" value="{{ old('jumlah_korban', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Terdampak</label>
            <input type="number" name="jumlah_terdampak" value="{{ old('jumlah_terdampak', 0) }}" class="form-control" required>
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
