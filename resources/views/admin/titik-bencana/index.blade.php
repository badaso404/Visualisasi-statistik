@extends('admin.layout.app')
@section('title', 'Titik Peta Bencana')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0">Titik Peta Bencana</h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <a href="{{ route('admin.titik-bencana.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#importBox"><i class="bi bi-upload"></i> Import CSV</button>
        <button class="btn btn-primary btn-sm"
                data-modal-form="#modalTitikBencana"
                data-action="{{ route('admin.titik-bencana.store') }}"
                data-title="Tambah Titik Bencana">
            <i class="bi bi-plus-lg"></i> Tambah
        </button>
    </div>
</div>

<div class="collapse mb-3" id="importBox">
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

<p class="text-muted small">Titik referensi pada peta sebaran bencana: zona rawan banjir (Prioritas 1–3), pos Damkar, dan zona aman evakuasi.</p>

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
                @forelse ($items as $item)
                    <tr>
                        <td>{{ \App\Models\TitikBencana::KATEGORI[$item->kategori] ?? $item->kategori }}</td>
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
</script>
@endsection
