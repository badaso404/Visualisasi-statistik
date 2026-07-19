@extends('admin.layout.app')
@section('title', 'Infrastruktur Digital')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jakwifi" type="button">JakWiFi <span class="badge bg-secondary">{{ $jakWifi->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cctv" type="button">CCTV <span class="badge bg-secondary">{{ $cctv->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= JakWiFi ================= --}}
    <div class="tab-pane fade show active" id="tab-jakwifi">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">JakWiFi per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.jak-wifi" judul="JakWiFi" />
                <a href="{{ route('admin.jak-wifi.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalJakWifi"
                        data-action="{{ route('admin.jak-wifi.store') }}"
                        data-title="Tambah JakWiFi">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalJakWifi"
                                            data-action="{{ route('admin.jak-wifi.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit JakWiFi {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_titik', 'titik_aktif', 'jumlah_pengguna', 'keterangan'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.jak-wifi.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="JakWiFi {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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

    {{-- ================= CCTV ================= --}}
    <div class="tab-pane fade" id="tab-cctv">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">CCTV per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.cctv" judul="CCTV" />
                <a href="{{ route('admin.cctv.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalCctv"
                        data-action="{{ route('admin.cctv.store') }}"
                        data-title="Tambah CCTV">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalCctv"
                                            data-action="{{ route('admin.cctv.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit CCTV {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_unit', 'unit_aktif', 'terintegrasi', 'keterangan'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.cctv.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="CCTV {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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
<x-admin.modal-form id="modalJakWifi" title="Tambah JakWiFi" :action="route('admin.jak-wifi.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_id" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ($kecamatan as $k)
                    <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Titik</label>
            <input type="number" name="jumlah_titik" value="{{ old('jumlah_titik', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Titik Aktif</label>
            <input type="number" name="titik_aktif" value="{{ old('titik_aktif', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Pengguna</label>
            <input type="number" name="jumlah_pengguna" value="{{ old('jumlah_pengguna', 0) }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
            <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalCctv" title="Tambah CCTV" :action="route('admin.cctv.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_id" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ($kecamatan as $k)
                    <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Unit</label>
            <input type="number" name="jumlah_unit" value="{{ old('jumlah_unit', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Unit Aktif</label>
            <input type="number" name="unit_aktif" value="{{ old('unit_aktif', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Terintegrasi</label>
            <input type="number" name="terintegrasi" value="{{ old('terintegrasi', 0) }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
            <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>
@endsection
