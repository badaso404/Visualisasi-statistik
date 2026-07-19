@extends('admin.layout.app')
@section('title', 'Kependudukan')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan" type="button">Ringkasan Tahunan</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kecamatan" type="button">Per Kecamatan <span class="badge bg-secondary">{{ $perKecamatan->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kelurahan" type="button">Per Kelurahan <span class="badge bg-secondary">{{ $perKelurahan->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= Ringkasan tahunan ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Kependudukan (ringkasan per tahun)</h6>
            <button class="btn btn-primary btn-sm"
                    data-modal-form="#modalKependudukan"
                    data-action="{{ route('admin.kependudukan.store') }}"
                    data-title="Tambah Kependudukan">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </div>
        <div class="card border-0 shadow-sm">
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalKependudukan"
                                            data-action="{{ route('admin.kependudukan.update', $item) }}"
                                            data-method="PUT"
                                            data-title="Edit Kependudukan {{ $item->tahun }}"
                                            data-fields="{{ json_encode($item->only(['tahun', 'jumlah_laki_laki', 'jumlah_perempuan', 'jumlah_total', 'sumber'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.kependudukan.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="data kependudukan tahun {{ $item->tahun }}">
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
    </div>

    {{-- ================= Per kecamatan ================= --}}
    <div class="tab-pane fade" id="tab-kecamatan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Penduduk per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.penduduk-kecamatan" judul="Penduduk per Kecamatan" />
                <a href="{{ route('admin.penduduk-kecamatan.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalPendudukKecamatan"
                        data-action="{{ route('admin.penduduk-kecamatan.store') }}"
                        data-title="Tambah Penduduk Kecamatan">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalPendudukKecamatan"
                                            data-action="{{ route('admin.penduduk-kecamatan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_penduduk'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.penduduk-kecamatan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="penduduk {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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
    </div>

    {{-- ================= Per kelurahan ================= --}}
    <div class="tab-pane fade" id="tab-kelurahan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Penduduk per Kelurahan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.penduduk-kelurahan.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
                <a href="{{ route('admin.penduduk-kelurahan.template') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-arrow-down"></i> Template</a>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportKelurahan"><i class="bi bi-upload"></i> Import CSV</button>
                <button class="btn btn-primary btn-sm"
                        data-modal-form="#modalPendudukKelurahan"
                        data-action="{{ route('admin.penduduk-kelurahan.store') }}"
                        data-title="Tambah Penduduk Kelurahan">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalPendudukKelurahan"
                                            data-action="{{ route('admin.penduduk-kelurahan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit {{ $row->nama_kelurahan }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'nama_kelurahan', 'tahun', 'jumlah_penduduk', 'latitude', 'longitude'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.penduduk-kelurahan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="kelurahan {{ $row->nama_kelurahan }} tahun {{ $row->tahun }}">
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
    </div>
</div>

{{-- ================= Modal ================= --}}
<x-admin.modal-form id="modalKependudukan" title="Tambah Kependudukan" :action="route('admin.kependudukan.store')">
    <div class="mb-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Jumlah Laki-laki</label>
        <input type="number" name="jumlah_laki_laki" value="{{ old('jumlah_laki_laki') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Jumlah Perempuan</label>
        <input type="number" name="jumlah_perempuan" value="{{ old('jumlah_perempuan') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Jumlah Total</label>
        <input type="number" name="jumlah_total" value="{{ old('jumlah_total') }}" class="form-control" required>
    </div>
    <div>
        <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
        <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalPendudukKecamatan" title="Tambah Penduduk Kecamatan" :action="route('admin.penduduk-kecamatan.store')">
    <div class="mb-3">
        <label class="form-label">Kecamatan</label>
        <select name="kecamatan_id" class="form-select" required>
            <option value="">— pilih —</option>
            @foreach ($kecamatan as $k)
                <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
    </div>
    <div>
        <label class="form-label">Jumlah Penduduk</label>
        <input type="number" name="jumlah_penduduk" value="{{ old('jumlah_penduduk') }}" class="form-control" required>
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalPendudukKelurahan" title="Tambah Penduduk Kelurahan" :action="route('admin.penduduk-kelurahan.store')">
    <div class="mb-3">
        <label class="form-label">Kecamatan</label>
        <select name="kecamatan_id" class="form-select" required>
            <option value="">— pilih —</option>
            @foreach ($kecamatan as $k)
                <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Kelurahan</label>
        <input type="text" name="nama_kelurahan" value="{{ old('nama_kelurahan') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Jumlah Penduduk</label>
        <input type="number" name="jumlah_penduduk" value="{{ old('jumlah_penduduk') }}" class="form-control" required>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Latitude <span class="text-muted">(opsional)</span></label>
            <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Longitude <span class="text-muted">(opsional)</span></label>
            <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>

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
@endsection
