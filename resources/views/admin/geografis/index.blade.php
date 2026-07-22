@extends('admin.layout.app')
@section('title', 'Data Geografis')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan" type="button">Ringkasan</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-luas" type="button">Luas per Kecamatan <span class="badge bg-secondary">{{ $luas->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= Ringkasan ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Data Geografis (ringkasan)</h6>
            <div class="d-flex gap-2 flex-wrap">
            <x-admin.sync-bps modul="geografis" isi="jumlah kelurahan/RW/RT per kecamatan" />
            <button class="btn btn-primary btn-sm"
                    data-modal-form="#modalGeografis"
                    data-action="{{ route('admin.geografis.store') }}"
                    data-title="Tambah Data Geografis">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
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
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalGeografis"
                                            data-action="{{ route('admin.geografis.update', $item) }}"
                                            data-method="PUT"
                                            data-title="Edit Data Geografis {{ $item->tahun }}"
                                            data-fields="{{ json_encode($item->only(['tahun', 'luas_kota_km2', 'ketinggian_mdpl', 'sumber'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.geografis.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="data geografis tahun {{ $item->tahun }}">
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

    {{-- ================= Luas per kecamatan ================= --}}
    <div class="tab-pane fade" id="tab-luas">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Luas per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.luas-kecamatan" judul="Luas per Kecamatan" kunci="kecamatan + tahun" />
                <button class="btn btn-primary btn-sm"
                        data-modal-form="#modalLuasKecamatan"
                        data-action="{{ route('admin.luas-kecamatan.store') }}"
                        data-title="Tambah Luas Kecamatan">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kecamatan</th><th>Tahun Data</th><th>Luas (km²)</th><th>Persentase (%)</th>
                            <th>Kelurahan</th><th>RW</th><th>RT</th><th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($luas as $row)
                            <tr>
                                <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                                <td>{{ $row->dataGeografis->tahun ?? '-' }}</td>
                                <td>{{ $row->luas_km2 }}</td>
                                <td>{{ $row->persentase }}</td>
                                <td>{{ $row->jumlah_kelurahan ?? '-' }}</td>
                                <td>{{ $row->jumlah_rw ?? '-' }}</td>
                                <td>{{ $row->jumlah_rt ?? '-' }}</td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalLuasKecamatan"
                                            data-action="{{ route('admin.luas-kecamatan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit Luas {{ $row->kecamatan->nama_kecamatan ?? '' }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'data_geografis_id', 'luas_km2', 'persentase', 'jumlah_kelurahan', 'jumlah_rw', 'jumlah_rt'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.luas-kecamatan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="luas {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->dataGeografis->tahun ?? '-' }}">
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
<x-admin.modal-form id="modalGeografis" title="Tambah Data Geografis" :action="route('admin.geografis.store')">
    <div class="mb-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Luas Kota (km²)</label>
        <input type="number" step="0.01" name="luas_kota_km2" value="{{ old('luas_kota_km2') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Ketinggian (mdpl)</label>
        <input type="number" name="ketinggian_mdpl" value="{{ old('ketinggian_mdpl') }}" class="form-control" required>
    </div>
    <div>
        <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
        <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalLuasKecamatan" title="Tambah Luas Kecamatan" :action="route('admin.luas-kecamatan.store')">
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
        <label class="form-label">Tahun Data Geografis</label>
        <select name="data_geografis_id" class="form-select" required>
            <option value="">— pilih —</option>
            @foreach ($items as $g)
                <option value="{{ $g->id }}" @selected(old('data_geografis_id') == $g->id)>{{ $g->tahun }}</option>
            @endforeach
        </select>
        @if ($items->isEmpty())
            <div class="form-text text-warning">Tambahkan data geografis (tab Ringkasan) terlebih dahulu.</div>
        @endif
    </div>
    <div class="mb-3">
        <label class="form-label">Luas (km²)</label>
        <input type="number" step="0.01" name="luas_km2" value="{{ old('luas_km2') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Persentase (%)</label>
        <input type="number" step="0.01" name="persentase" value="{{ old('persentase') }}" class="form-control" required>
    </div>
    {{--
        Kelurahan/RW/RT bersumber BPS var 155 dan boleh kosong. Sebelumnya hanya
        bisa diisi lewat seeder padahal ikut tampil di halaman publik, sehingga
        koreksi kecil pun mengharuskan akses server.
    --}}
    <div class="row g-2">
        <div class="col-4">
            <label class="form-label">Kelurahan</label>
            <input type="number" min="0" name="jumlah_kelurahan" value="{{ old('jumlah_kelurahan') }}" class="form-control" placeholder="opsional">
        </div>
        <div class="col-4">
            <label class="form-label">RW</label>
            <input type="number" min="0" name="jumlah_rw" value="{{ old('jumlah_rw') }}" class="form-control" placeholder="opsional">
        </div>
        <div class="col-4">
            <label class="form-label">RT</label>
            <input type="number" min="0" name="jumlah_rt" value="{{ old('jumlah_rt') }}" class="form-control" placeholder="opsional">
        </div>
    </div>
</x-admin.modal-form>
@endsection
