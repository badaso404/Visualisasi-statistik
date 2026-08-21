@extends('admin.layout.app')
@section('title', 'Fasilitas Umum')

@section('content')

{{-- ── Ringkasan & sinkronisasi ─────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h6 class="mb-1">Fasilitas Umum Jakarta Barat</h6>
                <div class="small text-muted">
                    Total <b>{{ number_format($daftar->total(), 0, ',', '.') }}</b> fasilitas tersaring
                    dari {{ number_format(array_sum($jumlahKategori->all()), 0, ',', '.') }} baris.
                    @if ($terakhirSinkron)
                        Terakhir diperbarui {{ \Carbon\Carbon::parse($terakhirSinkron)->translatedFormat('d F Y, H:i') }}.
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <x-admin.csv-tools prefix="admin.fasilitas-umum" judul="Fasilitas Umum" kunci="kategori + nama" />
                <button class="btn btn-primary btn-sm" data-modal-form="#modalFasilitas"
                        data-action="{{ route('admin.fasilitas-umum.store') }}"
                        data-title="Tambah Fasilitas">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
        </div>

        @if ($tanpaKecamatan > 0)
            {{-- Sumber tidak selalu menyebut wilayah. Yang tersisa ditunjukkan
                 di sini karena fasilitas tanpa kecamatan tidak muncul di grafik
                 sebaran halaman publik — mudah luput kalau tidak diberitahu. --}}
            <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3 mb-0 py-2">
                <span class="small">
                    <i class="bi bi-exclamation-triangle"></i>
                    <b>{{ $tanpaKecamatan }}</b> fasilitas belum punya kecamatan, jadi belum ikut terhitung di grafik sebaran.
                </span>
                <a href="{{ route('admin.fasilitas-umum.index', ['kecamatan_id' => 'kosong']) }}" class="btn btn-sm btn-outline-dark">
                    Tinjau
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ── Tarik data dari sumber ───────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form action="{{ route('admin.fasilitas-umum.sync') }}" method="POST"
              class="row g-2 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label small mb-1">Tarik dari situs kecamatan Jakarta Barat</label>
                <select name="kategori" class="form-select form-select-sm" required>
                    @foreach (\App\Models\FasilitasUmum::KATEGORI as $slug => $label)
                        <option value="{{ $slug }}">{{ $label }} ({{ $jumlahKategori[$slug] ?? 0 }} tersimpan)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-cloud-download"></i> Sinkronkan Kategori Ini
                </button>
            </div>
            <div class="col-md">
                <div class="small text-muted">
                    Satu kategori sekali jalan — sumber membatasi 60 permintaan/menit.
                    Kategori kecil selesai dalam hitungan detik, tapi <b>Tempat Ibadah</b> (600+ data)
                    butuh ~70 detik dan bisa terputus batas waktu server web.
                    Untuk kategori itu, atau untuk menarik semuanya sekaligus, jalankan dari terminal:
                    <code>php artisan statistik:sinkron-fasilitas</code>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Penyaring ────────────────────────────────────────────────── --}}
<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Kategori</label>
            <select name="kategori" class="form-select form-select-sm">
                <option value="">Semua kategori</option>
                @foreach (\App\Models\FasilitasUmum::KATEGORI as $slug => $label)
                    <option value="{{ $slug }}" @selected($kategori === $slug)>
                        {{ $label }} ({{ $jumlahKategori[$slug] ?? 0 }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Kecamatan</label>
            <select name="kecamatan_id" class="form-select form-select-sm">
                <option value="">Semua kecamatan</option>
                <option value="kosong" @selected(request('kecamatan_id') === 'kosong')>— belum terpetakan —</option>
                @foreach ($kecamatan as $k)
                    <option value="{{ $k->id }}" @selected(request('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small mb-1">Cari nama / alamat</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                   placeholder="mis. GOR, Kalideres, Jl. Panjang">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-secondary btn-sm w-100"><i class="bi bi-search"></i> Saring</button>
            @if (request()->hasAny(['kategori', 'kecamatan_id', 'q']))
                <a href="{{ route('admin.fasilitas-umum.index') }}" class="btn btn-outline-secondary btn-sm">×</a>
            @endif
        </div>
    </div>
</form>

{{-- ── Tabel ────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kategori</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kecamatan</th>
                    <th>Kelurahan</th>
                    <th>Koordinat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $row)
                    <tr>
                        <td class="text-nowrap">
                            <span class="badge" style="background: {{ $row->warna() }}">
                                <i class="fa {{ $row->ikon() }}"></i> {{ $row->labelKategori() }}
                            </span>
                        </td>
                        <td>{{ $row->nama }}</td>
                        <td class="small text-muted" style="max-width:320px">{{ $row->alamat ?: '-' }}</td>
                        <td class="text-nowrap">
                            @if ($row->kecamatan)
                                {{ $row->kecamatan->nama_kecamatan }}
                            @else
                                <span class="badge bg-warning text-dark">belum diisi</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $row->kelurahan ?: '-' }}</td>
                        <td class="small text-nowrap">
                            @if ($row->latitude && $row->longitude)
                                {{ $row->latitude }}, {{ $row->longitude }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="#modalFasilitas"
                                    data-action="{{ route('admin.fasilitas-umum.update', $row) }}"
                                    data-method="PUT"
                                    data-title="Edit {{ $row->nama }}"
                                    data-fields="{{ json_encode($row->only(['kategori', 'nama', 'alamat', 'kecamatan_id', 'kelurahan', 'latitude', 'longitude'])) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.fasilitas-umum.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="{{ $row->nama }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">
                        Belum ada data yang cocok. Tarik dulu lewat tombol Sinkronkan di atas.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($daftar->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $daftar->links() }}
        </div>
    @endif
</div>

{{-- ── Modal tambah/edit ────────────────────────────────────────── --}}
<x-admin.modal-form id="modalFasilitas" title="Tambah Fasilitas"
                    :action="route('admin.fasilitas-umum.store')" size="modal-lg">
    <div class="row g-3">
        <div class="col-md-5">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select" required>
                @foreach (\App\Models\FasilitasUmum::KATEGORI as $slug => $label)
                    <option value="{{ $slug }}" @selected(old('kategori') === $slug)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-7">
            <label class="form-label">Nama Fasilitas</label>
            <input type="text" name="nama" value="{{ old('nama') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Alamat <span class="text-muted">(opsional)</span></label>
            <input type="text" name="alamat" value="{{ old('alamat') }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_id" class="form-select">
                <option value="">— belum diisi —</option>
                @foreach ($kecamatan as $k)
                    <option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama_kecamatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Kelurahan <span class="text-muted">(opsional)</span></label>
            <input type="text" name="kelurahan" value="{{ old('kelurahan') }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Latitude <span class="text-muted">(opsional)</span></label>
            <input type="text" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="-6.1683">
        </div>
        <div class="col-md-6">
            <label class="form-label">Longitude <span class="text-muted">(opsional)</span></label>
            <input type="text" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="106.7853">
        </div>
        <div class="col-12">
            <div class="small text-muted">
                Koordinat tidak dikirim oleh sumber data. Isian ini yang menentukan apakah
                fasilitas muncul sebagai titik di peta halaman publik.
            </div>
        </div>
    </div>
</x-admin.modal-form>
@endsection
