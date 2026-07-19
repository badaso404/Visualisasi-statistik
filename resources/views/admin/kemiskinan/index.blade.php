@extends('admin.layout.app')
@section('title', 'Kemiskinan')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan" type="button">Ringkasan Tahunan</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kecamatan" type="button">Per Kecamatan <span class="badge bg-secondary">{{ $perKecamatan->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= Ringkasan tahunan ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Kemiskinan (ringkasan per tahun)</h6>
            <button class="btn btn-primary btn-sm"
                    data-modal-form="#modalKemiskinan"
                    data-action="{{ route('admin.kemiskinan.store') }}"
                    data-title="Tambah Kemiskinan">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Tahun</th>
                            <th>Penduduk Miskin</th><th>Persentase</th><th>Garis Kemiskinan</th>
                            <th>P1 (Kedalaman)</th><th>P2 (Keparahan)</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->tahun }}</td>
                                <td>{{ number_format($item->jumlah_penduduk_miskin, 0, ',', '.') }} jiwa</td>
                                <td>{{ $item->persentase_penduduk_miskin }}%</td>
                                <td>Rp {{ number_format($item->garis_kemiskinan, 0, ',', '.') }}</td>
                                <td>{{ $item->indeks_kedalaman }}</td>
                                <td>{{ $item->indeks_keparahan }}</td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalKemiskinan"
                                            data-action="{{ route('admin.kemiskinan.update', $item) }}"
                                            data-method="PUT"
                                            data-title="Edit Kemiskinan {{ $item->tahun }}"
                                            data-fields="{{ json_encode($item->only(['tahun', 'jumlah_penduduk_miskin', 'persentase_penduduk_miskin', 'garis_kemiskinan', 'indeks_kedalaman', 'indeks_keparahan', 'sumber'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.kemiskinan.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="data kemiskinan tahun {{ $item->tahun }}">
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

    {{-- ================= Per kecamatan ================= --}}
    <div class="tab-pane fade" id="tab-kecamatan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Kemiskinan per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.kemiskinan-kecamatan" judul="Kemiskinan per Kecamatan" />
                <a href="{{ route('admin.kemiskinan-kecamatan.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalKemiskinanKecamatan"
                        data-action="{{ route('admin.kemiskinan-kecamatan.store') }}"
                        data-title="Tambah Kemiskinan Kecamatan">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kecamatan</th><th>Tahun</th>
                            <th>Penduduk Miskin</th><th>Keluarga Miskin (KK)</th>
                            <th>Penerima Bantuan</th><th>Persentase</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perKecamatan as $row)
                            <tr>
                                <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                                <td>{{ $row->tahun }}</td>
                                <td>{{ number_format($row->jumlah_penduduk_miskin, 0, ',', '.') }}</td>
                                <td>{{ number_format($row->jumlah_keluarga_miskin, 0, ',', '.') }}</td>
                                <td>{{ number_format($row->penerima_bantuan, 0, ',', '.') }}</td>
                                <td>{{ $row->persentase }}%</td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalKemiskinanKecamatan"
                                            data-action="{{ route('admin.kemiskinan-kecamatan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_penduduk_miskin', 'jumlah_keluarga_miskin', 'penerima_bantuan', 'persentase'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.kemiskinan-kecamatan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="kemiskinan {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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
<x-admin.modal-form id="modalKemiskinan" title="Tambah Kemiskinan" :action="route('admin.kemiskinan.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Jumlah Penduduk Miskin <span class="text-muted">(jiwa)</span></label>
            <input type="number" name="jumlah_penduduk_miskin" value="{{ old('jumlah_penduduk_miskin') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Persentase Penduduk Miskin (%)</label>
            <input type="number" step="0.01" name="persentase_penduduk_miskin" value="{{ old('persentase_penduduk_miskin') }}" class="form-control" min="0" required>
        </div>
        <div class="col-12">
            <label class="form-label">Garis Kemiskinan <span class="text-muted">(Rp per kapita/bulan)</span></label>
            <input type="number" step="0.01" name="garis_kemiskinan" value="{{ old('garis_kemiskinan') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Indeks Kedalaman (P1)</label>
            <input type="number" step="0.01" name="indeks_kedalaman" value="{{ old('indeks_kedalaman') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Indeks Keparahan (P2)</label>
            <input type="number" step="0.01" name="indeks_keparahan" value="{{ old('indeks_keparahan') }}" class="form-control" min="0" required>
        </div>
        <div class="col-12">
            <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
            <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalKemiskinanKecamatan" title="Tambah Kemiskinan Kecamatan" :action="route('admin.kemiskinan-kecamatan.store')">
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
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Penduduk Miskin <span class="text-muted">(jiwa)</span></label>
            <input type="number" name="jumlah_penduduk_miskin" value="{{ old('jumlah_penduduk_miskin') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Keluarga Miskin <span class="text-muted">(KK)</span></label>
            <input type="number" name="jumlah_keluarga_miskin" value="{{ old('jumlah_keluarga_miskin') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Penerima Bantuan <span class="text-muted">(jiwa)</span></label>
            <input type="number" name="penerima_bantuan" value="{{ old('penerima_bantuan') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Persentase (%)</label>
            <input type="number" step="0.01" name="persentase" value="{{ old('persentase') }}" class="form-control" min="0" required>
        </div>
    </div>
</x-admin.modal-form>
@endsection
