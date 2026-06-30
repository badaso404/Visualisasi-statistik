@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Titik Bencana' : 'Tambah Titik Bencana')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.titik-bencana.update', $item) : route('admin.titik-bencana.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" id="kategori" class="form-select" required>
                        <option value="">— pilih —</option>
                        @foreach ($kategoriList as $key => $label)
                            <option value="{{ $key }}" @selected(old('kategori', $item->kategori) == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6" id="level-wrap">
                    <label class="form-label">Level Prioritas <span class="text-muted">(khusus banjir)</span></label>
                    <select name="level" class="form-select">
                        <option value="">— pilih —</option>
                        <option value="1" @selected(old('level', $item->level) == 1)>Prioritas 1 — Rawan tinggi (&gt; 50 cm)</option>
                        <option value="2" @selected(old('level', $item->level) == 2)>Prioritas 2 — Rawan sedang (20–50 cm)</option>
                        <option value="3" @selected(old('level', $item->level) == 3)>Prioritas 3 — Rawan rendah (&lt; 20 cm)</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Lokasi</label>
                    <input type="text" name="nama" value="{{ old('nama', $item->nama) }}" class="form-control" placeholder="mis. Pos Damkar Cengkareng" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kecamatan <span class="text-muted">(opsional)</span></label>
                    <select name="kecamatan_id" class="form-select">
                        <option value="">— pilih —</option>
                        @foreach ($kecamatan as $k)
                            <option value="{{ $k->id }}" @selected(old('kecamatan_id', $item->kecamatan_id) == $k->id)>{{ $k->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12" id="link-wrap">
                    <label class="form-label">Link Google Maps <span class="text-muted">(opsional)</span></label>
                    <input type="url" name="link_maps" value="{{ old('link_maps', $item->link_maps) }}" class="form-control" placeholder="https://maps.app.goo.gl/... atau https://www.google.com/maps/...">
                    <small class="text-muted">Tempel link lokasi dari Google Maps. Dipakai untuk tombol "Buka Maps" di peta publik. Kalau kosong, tombol memakai koordinat di bawah.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="any" name="latitude" value="{{ old('latitude', $item->latitude) }}" class="form-control" placeholder="-6.183454092199373" required>
                    <small class="text-muted">Salin dari Google Maps (klik kanan lokasi &rarr; klik koordinatnya).</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="any" name="longitude" value="{{ old('longitude', $item->longitude) }}" class="form-control" placeholder="106.73891072264179" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
                    <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.titik-bencana.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Tampilkan field Level hanya saat kategori = Zona Rawan Banjir
    (function () {
        var kategori = document.getElementById('kategori');
        var levelWrap = document.getElementById('level-wrap');
        var linkWrap = document.getElementById('link-wrap');
        function toggleFields() {
            var isBanjir = kategori.value === 'banjir_rawan';
            // Level hanya untuk banjir; link Maps untuk selain banjir (tidak perlu buka Maps)
            levelWrap.style.display = isBanjir ? '' : 'none';
            linkWrap.style.display = isBanjir ? 'none' : '';
        }
        kategori.addEventListener('change', toggleFields);
        toggleFields();
    })();
</script>
@endsection
