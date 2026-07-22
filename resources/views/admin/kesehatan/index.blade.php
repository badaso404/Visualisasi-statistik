@extends('admin.layout.app')
@section('title', 'Kesehatan')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan" type="button">Ringkasan Tahunan</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tenaga" type="button">Tenaga Kesehatan <span class="badge bg-secondary">{{ $tenaga->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-fasilitas" type="button">Fasilitas Kesehatan <span class="badge bg-secondary">{{ $fasilitas->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= Ringkasan tahunan ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Kesehatan (ringkasan per tahun)</h6>
            <div class="d-flex gap-2 flex-wrap">
            <x-admin.sync-bps modul="kesehatan" isi="tenaga & fasilitas kesehatan per kecamatan" />
            <button class="btn btn-primary btn-sm"
                    data-modal-form="#modalKesehatan"
                    data-action="{{ route('admin.kesehatan.store') }}"
                    data-title="Tambah Kesehatan">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalKesehatan"
                                            data-action="{{ route('admin.kesehatan.update', $item) }}"
                                            data-method="PUT"
                                            data-title="Edit Kesehatan {{ $item->tahun }}"
                                            data-fields="{{ json_encode($item->only(['tahun', 'jumlah_tempat_tidur_rs', 'cakupan_imunisasi_dasar', 'sumber'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.kesehatan.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="data kesehatan tahun {{ $item->tahun }}">
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
    </div>

    {{-- ================= Tenaga kesehatan ================= --}}
    <div class="tab-pane fade" id="tab-tenaga">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Tenaga Kesehatan per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.tenaga-kesehatan" judul="Tenaga Kesehatan" />
                <a href="{{ route('admin.tenaga-kesehatan.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalTenaga"
                        data-action="{{ route('admin.tenaga-kesehatan.store') }}"
                        data-title="Tambah Tenaga Kesehatan">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalTenaga"
                                            data-action="{{ route('admin.tenaga-kesehatan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_total', 'dokter', 'perawat', 'bidan', 'ahli_gizi', 'farmasi'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.tenaga-kesehatan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="tenaga kesehatan {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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
    </div>

    {{-- ================= Fasilitas kesehatan ================= --}}
    <div class="tab-pane fade" id="tab-fasilitas">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Fasilitas Kesehatan per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.fasilitas-kesehatan" judul="Fasilitas Kesehatan" />
                <a href="{{ route('admin.fasilitas-kesehatan.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalFasilitas"
                        data-action="{{ route('admin.fasilitas-kesehatan.store') }}"
                        data-title="Tambah Fasilitas Kesehatan">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
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
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalFasilitas"
                                            data-action="{{ route('admin.fasilitas-kesehatan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_total', 'klinik_kesehatan', 'posyandu', 'puskesmas', 'rumah_sakit'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.fasilitas-kesehatan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="fasilitas kesehatan {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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
    </div>
</div>

{{-- ================= Modal ================= --}}
<x-admin.modal-form id="modalKesehatan" title="Tambah Kesehatan" :action="route('admin.kesehatan.store')">
    <div class="mb-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Jumlah Tempat Tidur RS</label>
        <input type="number" name="jumlah_tempat_tidur_rs" value="{{ old('jumlah_tempat_tidur_rs') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Cakupan Imunisasi Dasar (%) <span class="text-muted">(opsional — tak tersedia di BPS)</span></label>
        <input type="number" step="0.01" name="cakupan_imunisasi_dasar" value="{{ old('cakupan_imunisasi_dasar') }}" class="form-control">
    </div>
    <div>
        <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
        <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalTenaga" title="Tambah Tenaga Kesehatan" :action="route('admin.tenaga-kesehatan.store')" size="modal-lg">
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
            <x-admin.tahun-induk :induk="$items" sebutan="ringkasan kesehatan" tab="tab Ringkasan" />
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Total</label>
            <input type="number" name="jumlah_total" value="{{ old('jumlah_total') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Dokter</label>
            <input type="number" name="dokter" value="{{ old('dokter', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Perawat</label>
            <input type="number" name="perawat" value="{{ old('perawat', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Bidan</label>
            <input type="number" name="bidan" value="{{ old('bidan', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ahli Gizi</label>
            <input type="number" name="ahli_gizi" value="{{ old('ahli_gizi', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Farmasi</label>
            <input type="number" name="farmasi" value="{{ old('farmasi', 0) }}" class="form-control" required>
        </div>
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalFasilitas" title="Tambah Fasilitas Kesehatan" :action="route('admin.fasilitas-kesehatan.store')" size="modal-lg">
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
            <x-admin.tahun-induk :induk="$items" sebutan="ringkasan kesehatan" tab="tab Ringkasan" />
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Total</label>
            <input type="number" name="jumlah_total" value="{{ old('jumlah_total') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Klinik Kesehatan</label>
            <input type="number" name="klinik_kesehatan" value="{{ old('klinik_kesehatan', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Posyandu</label>
            <input type="number" name="posyandu" value="{{ old('posyandu', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Puskesmas</label>
            <input type="number" name="puskesmas" value="{{ old('puskesmas', 0) }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Rumah Sakit</label>
            <input type="number" name="rumah_sakit" value="{{ old('rumah_sakit', 0) }}" class="form-control" required>
        </div>
    </div>
</x-admin.modal-form>
@endsection
