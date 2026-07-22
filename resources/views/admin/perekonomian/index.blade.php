@extends('admin.layout.app')
@section('title', 'Perekonomian')

@use('App\Models\DataPerekonomian')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ringkasan" type="button">Ringkasan Tahunan</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sektor" type="button">Lapangan Usaha <span class="badge bg-secondary">{{ $sektorPerTahun->flatten()->count() }}</span></button></li>
</ul>

<div class="tab-content">
    {{-- ================= Ringkasan tahunan ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">PDRB Jakarta Barat (ringkasan per tahun)</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.sync-bps modul="perekonomian" isi="PDRB dan 17 lapangan usaha" />
                <button class="btn btn-primary btn-sm"
                        data-modal-form="#modalPerekonomian"
                        data-action="{{ route('admin.perekonomian.store') }}"
                        data-title="Tambah Data Perekonomian">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Tahun</th>
                            <th class="text-end">PDRB ADHB</th>
                            <th class="text-end">PDRB ADHK 2010</th>
                            <th class="text-end">Pertumbuhan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    {{ $item->tahun }}
                                    @if ($batasPublik && $item->tahun >= $batasPublik)
                                        <span class="badge bg-success-subtle text-success-emphasis ms-1"
                                              title="Tahun ini bisa dipilih pengunjung di halaman publik">situs</span>
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->pdrb_adhb / 1000000, 2, ',', '.') }} T</td>
                                <td class="text-end">Rp {{ number_format($item->pdrb_adhk / 1000000, 2, ',', '.') }} T</td>
                                <td class="text-end {{ $item->laju_pertumbuhan < 0 ? 'text-danger fw-semibold' : '' }}">
                                    {{ number_format($item->laju_pertumbuhan, 2, ',', '.') }}%
                                </td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-modal-form="#modalPerekonomian"
                                            data-action="{{ route('admin.perekonomian.update', $item) }}"
                                            data-method="PUT"
                                            data-title="Edit Perekonomian {{ $item->tahun }}"
                                            data-fields="{{ json_encode($item->only(['tahun', 'pdrb_adhb', 'pdrb_adhk', 'laju_pertumbuhan', 'sumber'])) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.perekonomian.destroy', $item) }}" method="POST" class="d-inline"
                                          data-konfirmasi-hapus="data perekonomian tahun {{ $item->tahun }}">
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
        <p class="text-muted small mt-2 mb-0">
            Nilai PDRB disimpan dalam <strong>juta rupiah</strong> mengikuti satuan asli BPS; tabel di atas menampilkannya dalam triliun.
            @if ($batasPublik)
                Selektor tahun di halaman publik hanya memuat {{ DataPerekonomian::TAHUN_DITAMPILKAN }} tahun terakhir
                (mulai {{ $batasPublik }}) — ditandai <span class="badge bg-success-subtle text-success-emphasis">situs</span>;
                tahun sebelumnya tetap dipakai grafik tren.
            @endif
        </p>
    </div>

    {{-- ================= Lapangan usaha ================= --}}
    <div class="tab-pane fade" id="tab-sektor">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">PDRB menurut Lapangan Usaha</h6>
            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.pdrb-sektor" judul="PDRB per Lapangan Usaha"
                                   kunci="tahun + kode sektor" />
                <button class="btn btn-outline-primary btn-sm"
                        data-modal-form="#modalPdrbSektor"
                        data-action="{{ route('admin.pdrb-sektor.store') }}"
                        data-title="Tambah Lapangan Usaha">
                    <i class="bi bi-plus-lg"></i> Tambah Satuan
                </button>
            </div>
        </div>
        @if ($sektorPerTahun->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-4">Belum ada data.</div>
            </div>
        @else
            {{-- Tahun terbaru terbuka, sisanya terlipat: 17 baris per tahun terlalu
                 panjang kalau semuanya ditampilkan sekaligus. --}}
            <div class="accordion" id="akordeonSektor">
                @foreach ($sektorPerTahun as $tahun => $baris)
                    @php $pertama = $loop->first; @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $pertama ? '' : 'collapsed' }} py-2"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#sektor-{{ $tahun }}">
                                <span class="fw-semibold">{{ $tahun }}</span>
                                <span class="badge bg-secondary ms-2">{{ $baris->count() }} lapangan usaha</span>
                            </button>
                        </h2>
                        <div id="sektor-{{ $tahun }}"
                             class="accordion-collapse collapse {{ $pertama ? 'show' : '' }}"
                             data-bs-parent="#akordeonSektor">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kat.</th><th>Lapangan Usaha</th>
                                                <th class="text-end">ADHB (Rp M)</th>
                                                <th class="text-end">Distribusi</th><th class="text-end">Pertumbuhan</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($baris as $row)
                                                <tr>
                                                    <td>{{ $row->kategori }}</td>
                                                    <td>{{ $row->nama_sektor }}</td>
                                                    <td class="text-end">{{ number_format($row->adhb / 1000, 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($row->distribusi, 2, ',', '.') }}%</td>
                                                    <td class="text-end {{ $row->laju_pertumbuhan < 0 ? 'text-danger fw-semibold' : '' }}">
                                                        {{ number_format($row->laju_pertumbuhan, 2, ',', '.') }}%
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <button class="btn btn-sm btn-outline-primary"
                                                                data-modal-form="#modalPdrbSektor"
                                                                data-action="{{ route('admin.pdrb-sektor.update', $row) }}"
                                                                data-method="PUT"
                                                                data-title="Edit {{ $row->nama_sektor }} {{ $row->tahun }}"
                                                                data-fields="{{ json_encode($row->only(['tahun', 'kode_sektor', 'kategori', 'nama_sektor', 'adhb', 'distribusi', 'laju_pertumbuhan'])) }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.pdrb-sektor.destroy', $row) }}" method="POST" class="d-inline"
                                                              data-konfirmasi-hapus="{{ $row->nama_sektor }} tahun {{ $row->tahun }}">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ================= Modal ================= --}}
<x-admin.modal-form id="modalPerekonomian" title="Tambah Data Perekonomian" :action="route('admin.perekonomian.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun') }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">PDRB Harga Berlaku <span class="text-muted">(juta rupiah)</span></label>
            <input type="number" step="0.01" name="pdrb_adhb" value="{{ old('pdrb_adhb') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">PDRB Harga Konstan 2010 <span class="text-muted">(juta rupiah)</span></label>
            <input type="number" step="0.01" name="pdrb_adhk" value="{{ old('pdrb_adhk') }}" class="form-control" min="0" required>
        </div>
        <div class="col-12">
            <label class="form-label">Laju Pertumbuhan Ekonomi (%) <span class="text-muted">(boleh negatif)</span></label>
            <input type="number" step="0.01" name="laju_pertumbuhan" value="{{ old('laju_pertumbuhan') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
            <input type="text" name="sumber" value="{{ old('sumber') }}" class="form-control">
        </div>
    </div>
</x-admin.modal-form>

<x-admin.modal-form id="modalPdrbSektor" title="Tambah Lapangan Usaha" :action="route('admin.pdrb-sektor.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-4">
            <x-admin.tahun-induk :induk="$items" sebutan="ringkasan PDRB" tab="tab Ringkasan Tahunan" />
        </div>
        <div class="col-md-4">
            <label class="form-label">Kode Sektor <span class="text-muted">(1–17)</span></label>
            <input type="number" name="kode_sektor" value="{{ old('kode_sektor') }}" class="form-control" min="1" max="17" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Kategori <span class="text-muted">(A, B, …)</span></label>
            <input type="text" name="kategori" value="{{ old('kategori') }}" class="form-control" maxlength="12">
        </div>
        <div class="col-12">
            <label class="form-label">Nama Lapangan Usaha</label>
            <input type="text" name="nama_sektor" value="{{ old('nama_sektor') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">ADHB <span class="text-muted">(juta rupiah)</span></label>
            <input type="number" step="0.01" name="adhb" value="{{ old('adhb') }}" class="form-control" min="0" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Distribusi (%)</label>
            <input type="number" step="0.01" name="distribusi" value="{{ old('distribusi') }}" class="form-control" min="0" max="100" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Pertumbuhan (%) <span class="text-muted">(boleh negatif)</span></label>
            <input type="number" step="0.01" name="laju_pertumbuhan" value="{{ old('laju_pertumbuhan') }}" class="form-control" required>
        </div>
    </div>
</x-admin.modal-form>
@endsection
