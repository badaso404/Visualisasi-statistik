@extends('admin.layout.app')
@section('title', 'Kebencanaan')

@section('content')
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-rekap" type="button">Rekap Triwulanan <span class="badge bg-secondary">{{ $items->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-titik" type="button">Titik Peta Bencana <span class="badge bg-secondary">{{ $titik->count() }}</span></button></li>
</ul>

<div class="tab-content">
{{-- ================= Rekap triwulanan (data_bencana) ================= --}}
<div class="tab-pane fade show active" id="tab-rekap">
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h6 class="mb-0">Rekap Bencana Triwulanan</h6>
        <small class="text-muted">Jakarta Barat &middot; sumber: API Satu Data Jakarta</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <form action="{{ route('admin.bencana.sync') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-info btn-sm"><i class="bi bi-arrow-repeat"></i> Sync dari API</button>
        </form>
        <a href="{{ route('admin.bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importRekap"><i class="bi bi-upload"></i> Import CSV</button>
        <button class="btn btn-primary btn-sm"
                data-modal-form="#modalBencana"
                data-action="{{ route('admin.bencana.store') }}"
                data-title="Tambah Rekap Bencana">
            <i class="bi bi-plus-lg"></i> Tambah
        </button>
    </div>
</div>

<div class="collapse mb-3" id="importRekap">
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
</div>{{-- /tab-rekap --}}

{{-- ================= Titik peta bencana (titik_bencana) ================= --}}
<div class="tab-pane fade" id="tab-titik">
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h6 class="mb-0">Titik Peta Bencana</h6>
        <small class="text-muted">Zona rawan banjir (Prioritas 1–3), pos Damkar, dan zona aman evakuasi</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <a href="{{ route('admin.titik-bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importTitik"><i class="bi bi-upload"></i> Import CSV</button>
        <button class="btn btn-primary btn-sm"
                data-modal-form="#modalTitikBencana"
                data-action="{{ route('admin.titik-bencana.store') }}"
                data-title="Tambah Titik Bencana">
            <i class="bi bi-plus-lg"></i> Tambah
        </button>
    </div>
</div>

<div class="collapse mb-3" id="importTitik">
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
                @forelse ($titik as $item)
                    <tr>
                        <td>{{ $kategoriList[$item->kategori] ?? $item->kategori }}</td>
                        <td>{{ $item->level ? 'Prioritas ' . $item->level : '-' }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $item->latitude }}</td>
                        <td>{{ $item->longitude }}</td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="#modalTitikBencana"
                                    data-action="{{ route('admin.titik-bencana.update', $item) }}"
                                    data-method="PUT"
                                    data-title="Edit Titik — {{ $item->nama }}"
                                    data-fields="{{ json_encode($item->only(['kategori', 'level', 'nama', 'kecamatan_id', 'link_maps', 'latitude', 'longitude', 'keterangan'])) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.titik-bencana.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="titik {{ $item->nama }}">
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
</div>{{-- /tab-titik --}}
</div>{{-- /tab-content --}}

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

<x-admin.modal-form id="modalTitikBencana" title="Tambah Titik Bencana" :action="route('admin.titik-bencana.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select name="kategori" id="kategori" class="form-select" required>
                <option value="">— pilih —</option>
                @foreach ($kategoriList as $key => $label)
                    <option value="{{ $key }}" @selected(old('kategori') == $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6" id="level-wrap">
            <label class="form-label">Level Prioritas <span class="text-muted">(khusus banjir)</span></label>
            <select name="level" class="form-select">
                <option value="">— pilih —</option>
                <option value="1" @selected(old('level') == 1)>Prioritas 1 — Rawan tinggi (&gt; 50 cm)</option>
                <option value="2" @selected(old('level') == 2)>Prioritas 2 — Rawan sedang (20–50 cm)</option>
                <option value="3" @selected(old('level') == 3)>Prioritas 3 — Rawan rendah (&lt; 20 cm)</option>
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Nama Lokasi</label>
            <input type="text" name="nama" value="{{ old('nama') }}" class="form-control" placeholder="mis. Pos Damkar Cengkareng" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Kecamatan <span class="text-muted">(opsional)</span></label>
            <select name="kecamatan_id" class="form-select">
                <option value="">— pilih —</option>
                @foreach ($kecamatan as $k)
                    <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12" id="link-wrap">
            <label class="form-label">Link Google Maps <span class="text-muted">(opsional)</span></label>
            <input type="url" name="link_maps" value="{{ old('link_maps') }}" class="form-control" placeholder="https://maps.app.goo.gl/... atau https://www.google.com/maps/...">
            <small class="text-muted">Tempel link lokasi dari Google Maps. Dipakai untuk tombol "Buka Maps" di peta publik. Kalau kosong, tombol memakai koordinat di bawah.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Latitude</label>
            <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="-6.183454092199373" required>
            <small class="text-muted">Salin dari Google Maps (klik kanan lokasi &rarr; klik koordinatnya).</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Longitude</label>
            <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="106.73891072264179" required>
        </div>
        <div class="col-12">
            <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
            <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan') }}</textarea>
        </div>
    </div>
</x-admin.modal-form>

<script>
    // Level hanya relevan untuk zona rawan banjir; link Maps untuk kategori lain.
    (function () {
        var kategori  = document.getElementById('kategori');
        var levelWrap = document.getElementById('level-wrap');
        var linkWrap  = document.getElementById('link-wrap');

        function toggleFields() {
            var isBanjir = kategori.value === 'banjir_rawan';
            levelWrap.style.display = isBanjir ? '' : 'none';
            linkWrap.style.display  = isBanjir ? 'none' : '';
        }

        kategori.addEventListener('change', toggleFields);
        // Modal diisi oleh helper di layout sebelum ditampilkan, jadi ikut
        // menyesuaikan setiap kali modal dibuka (tambah maupun edit).
        document.getElementById('modalTitikBencana').addEventListener('show.bs.modal', toggleFields);
        toggleFields();
    })();

    // Ingat tab yang terakhir aktif (Rekap / Titik Peta), supaya setelah
    // submit form (tambah/edit/hapus) halaman tidak selalu balik ke tab
    // pertama — kembali ke tab tempat aksi itu dilakukan.
    //
    // Ditunda sampai DOMContentLoaded karena script Bootstrap (bootstrap.bundle.min.js)
    // dimuat di bagian bawah layout, SETELAH konten halaman ini dirender — kalau
    // dijalankan langsung, window.bootstrap belum ada saat baris ini dieksekusi.
    document.addEventListener('DOMContentLoaded', function () {
        var STORAGE_KEY = 'admin-bencana-active-tab';
        var navButtons = document.querySelectorAll('[data-bs-toggle="tab"]');

        navButtons.forEach(function (btn) {
            btn.addEventListener('shown.bs.tab', function () {
                sessionStorage.setItem(STORAGE_KEY, btn.getAttribute('data-bs-target'));
            });
        });

        var lastTab = sessionStorage.getItem(STORAGE_KEY);
        if (lastTab) {
            var target = document.querySelector('[data-bs-target="' + lastTab + '"]');
            if (target && window.bootstrap) {
                new bootstrap.Tab(target).show();
            }
        }
    });
</script>
@endsection
