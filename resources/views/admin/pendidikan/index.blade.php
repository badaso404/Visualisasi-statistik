@extends('admin.layout.app')
@section('title', 'Pendidikan')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan" type="button">Ringkasan APM/APK</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kecamatan" type="button">Per Kecamatan <span class="badge bg-secondary">{{ $perKecamatan->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= Ringkasan APM/APK ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Pendidikan (ringkasan APM/APK per tahun)</h6>
            <button class="btn btn-primary btn-sm"
                    data-modal-form="#modalPendidikan"
                    data-action="{{ route('admin.pendidikan.store') }}"
                    data-title="Tambah Pendidikan">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Tahun</th>
                            <th>APM SD/MI</th><th>APM SMP/MTs</th><th>APM SMA/SMK</th>
                            <th>APK SD/MI</th><th>APK SMP/MTs</th><th>APK SMA/SMK</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->tahun }}</td>
                                <td>{{ $item->apm_sd_mi }}</td>
                                <td>{{ $item->apm_smp_mts }}</td>
                                <td>{{ $item->apm_sma_smk_man }}</td>
                                <td>{{ $item->apk_sd_mi }}</td>
                                <td>{{ $item->apk_smp_mts }}</td>
                                <td>{{ $item->apk_sma_smk_man }}</td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalPendidikan"
                                            data-action="{{ route('admin.pendidikan.update', $item) }}"
                                            data-method="PUT"
                                            data-title="Edit Pendidikan {{ $item->tahun }}"
                                            data-fields="{{ json_encode($item->only(['tahun', 'apm_sd_mi', 'apm_smp_mts', 'apm_sma_smk_man', 'apk_sd_mi', 'apk_smp_mts', 'apk_sma_smk_man', 'sumber'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.pendidikan.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="data pendidikan tahun {{ $item->tahun }}">
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

    {{-- ================= Per kecamatan ================= --}}
    <div class="tab-pane fade" id="tab-kecamatan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Pendidikan per Kecamatan</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.pendidikan-kecamatan" judul="Pendidikan per Kecamatan" />
                <a href="{{ route('admin.pendidikan-kecamatan.batch') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-grid-3x3-gap"></i> Isi Massal per Tahun
                </a>
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalPendidikanKecamatan"
                        data-action="{{ route('admin.pendidikan-kecamatan.store') }}"
                        data-title="Tambah Pendidikan Kecamatan">
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
                            <th>Pelajar</th><th>Pendidik</th>
                            <th>Sekolah Negeri</th><th>Sekolah Swasta</th><th>Total Sekolah</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perKecamatan as $row)
                            <tr>
                                <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                                <td>{{ $row->tahun }}</td>
                                <td>{{ number_format($row->jumlah_pelajar, 0, ',', '.') }}</td>
                                <td>{{ number_format($row->jumlah_pendidik, 0, ',', '.') }}</td>
                                <td>{{ number_format($row->jumlah_sekolah_negeri, 0, ',', '.') }}</td>
                                <td>{{ number_format($row->jumlah_sekolah_swasta, 0, ',', '.') }}</td>
                                <td><strong>{{ number_format($row->jumlah_sekolah_negeri + $row->jumlah_sekolah_swasta, 0, ',', '.') }}</strong></td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalPendidikanKecamatan"
                                            data-action="{{ route('admin.pendidikan-kecamatan.update', $row) }}"
                                            data-method="PUT"
                                            data-title="Edit {{ $row->kecamatan->nama_kecamatan ?? '' }} {{ $row->tahun }}"
                                            data-fields="{{ json_encode($row->only(['kecamatan_id', 'tahun', 'jumlah_pelajar', 'jumlah_pendidik', 'jumlah_sekolah_negeri', 'jumlah_sekolah_swasta'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.pendidikan-kecamatan.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="pendidikan {{ $row->kecamatan->nama_kecamatan ?? '-' }} tahun {{ $row->tahun }}">
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
<x-admin.modal-form id="modalPendidikan" title="Tambah Pendidikan" :action="route('admin.pendidikan.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">APM SD/MI</label>
            <input type="number" step="0.01" name="apm_sd_mi" value="{{ old('apm_sd_mi') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">APM SMP/MTs</label>
            <input type="number" step="0.01" name="apm_smp_mts" value="{{ old('apm_smp_mts') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">APM SMA/SMK/MAN</label>
            <input type="number" step="0.01" name="apm_sma_smk_man" value="{{ old('apm_sma_smk_man') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">APK SD/MI</label>
            <input type="number" step="0.01" name="apk_sd_mi" value="{{ old('apk_sd_mi') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">APK SMP/MTs</label>
            <input type="number" step="0.01" name="apk_smp_mts" value="{{ old('apk_smp_mts') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">APK SMA/SMK/MAN</label>
            <input type="number" step="0.01" name="apk_sma_smk_man" value="{{ old('apk_sma_smk_man') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
            <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalPendidikanKecamatan" title="Tambah Pendidikan Kecamatan" :action="route('admin.pendidikan-kecamatan.store')">
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
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Jumlah Pelajar</label>
            <input type="number" name="jumlah_pelajar" value="{{ old('jumlah_pelajar') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Jumlah Pendidik</label>
            <input type="number" name="jumlah_pendidik" value="{{ old('jumlah_pendidik') }}" class="form-control" min="0" required>
        </div>
    </div>
    <label class="form-label fw-semibold">Jumlah Sekolah</label>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label text-muted small">Negeri</label>
            <input type="number" name="jumlah_sekolah_negeri" value="{{ old('jumlah_sekolah_negeri') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label text-muted small">Swasta</label>
            <input type="number" name="jumlah_sekolah_swasta" value="{{ old('jumlah_sekolah_swasta') }}" class="form-control" min="0" required>
        </div>
    </div>
    <div class="text-muted small mt-1">Total sekolah dihitung otomatis dari negeri + swasta.</div>
</x-admin.modal-form>
@endsection
