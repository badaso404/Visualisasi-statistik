@extends('admin.layout.app')
@section('title', 'Ngobrol Statistik')

@php
    // Modal per jenis konten; dipakai tombol Tambah dan tombol Edit di tabel.
    $modal = ['video' => '#modalVideo', 'infografis' => '#modalInfografis', 'materi' => '#modalMateri'];
    $ikon  = ['video' => 'bi-youtube', 'infografis' => 'bi-image', 'materi' => 'bi-book'];
@endphp

@section('content')

{{-- ── Ringkasan & tombol tambah ─────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h6 class="mb-1">Konten Ngobrol Statistik</h6>
                <div class="small text-muted">
                    Video YouTube, infografis, dan materi statistik yang tampil di halaman publik.
                    Total <b>{{ array_sum($jumlahKategori->all()) }}</b> konten.
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('statistik.ngobrol-statistik') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Lihat Halaman
                </a>
                @foreach (\App\Models\NgobrolStatistik::KATEGORI as $slug => $label)
                    <button class="btn btn-primary btn-sm" data-modal-form="{{ $modal[$slug] }}"
                            data-action="{{ route('admin.ngobrol-statistik.store') }}"
                            data-title="Tambah {{ $label }}"
                            data-fields='{{ json_encode(['kategori' => $slug, 'tampil' => 1, 'urutan' => 0]) }}'>
                        <i class="bi {{ $ikon[$slug] }}"></i> Tambah {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Penyaring ────────────────────────────────────────────────── --}}
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="{{ route('admin.ngobrol-statistik.index') }}"
       class="btn btn-sm {{ $kategori === null ? 'btn-dark' : 'btn-outline-dark' }}">
        Semua ({{ array_sum($jumlahKategori->all()) }})
    </a>
    @foreach (\App\Models\NgobrolStatistik::KATEGORI as $slug => $label)
        <a href="{{ route('admin.ngobrol-statistik.index', ['kategori' => $slug]) }}"
           class="btn btn-sm {{ $kategori === $slug ? 'btn-dark' : 'btn-outline-dark' }}">
            <i class="bi {{ $ikon[$slug] }}"></i> {{ $label }} ({{ $jumlahKategori[$slug] ?? 0 }})
        </a>
    @endforeach
</div>

{{-- ── Tabel ────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:140px">Pratinjau</th>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th class="text-center">Urutan</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $row)
                    @php $pratinjau = $row->kategori === 'video' ? $row->thumbnail() : $row->gambarUrl(); @endphp
                    <tr>
                        <td>
                            @if ($pratinjau)
                                <a href="{{ $row->watchUrl() ?? $row->gambarUrl() }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $pratinjau }}" alt="" class="rounded border"
                                         style="width:128px; aspect-ratio:16/9; object-fit:cover" loading="lazy">
                                </a>
                            @else
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted"
                                     style="width:128px; aspect-ratio:16/9">
                                    <i class="bi bi-file-text fs-4"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">
                                {{ $row->judul }}
                                @if ($row->instagram_url)
                                    <a href="{{ $row->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                       class="text-danger ms-1" title="Buka di Instagram"><i class="bi bi-instagram"></i></a>
                                @endif
                            </div>
                            @if ($row->deskripsi)
                                <div class="small text-muted" style="max-width:420px">{{ \Illuminate\Support\Str::limit($row->deskripsi, 120) }}</div>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <i class="bi {{ $ikon[$row->kategori] ?? 'bi-file' }}"></i>
                            {{ \App\Models\NgobrolStatistik::KATEGORI[$row->kategori] ?? $row->kategori }}
                        </td>
                        <td class="text-center">{{ $row->urutan }}</td>
                        <td class="text-center">
                            @if ($row->tampil)
                                <span class="badge bg-success">Tampil</span>
                            @else
                                <span class="badge bg-secondary">Disembunyikan</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="{{ $modal[$row->kategori] }}"
                                    data-action="{{ route('admin.ngobrol-statistik.update', $row) }}"
                                    data-method="PUT"
                                    data-title="Edit {{ $row->judul }}"
                                    data-fields="{{ json_encode([
                                        'kategori'    => $row->kategori,
                                        'judul'       => $row->judul,
                                        'deskripsi'   => $row->deskripsi,
                                        'youtube_url' => $row->watchUrl(),
                                        'instagram_url' => $row->instagram_url,
                                        'isi'         => $row->isi,
                                        'urutan'      => $row->urutan,
                                        'tampil'      => (int) $row->tampil,
                                    ]) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.ngobrol-statistik.destroy', $row) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="{{ $row->judul }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">
                        Belum ada konten. Pakai tombol <b>Tambah</b> di atas.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Modal: Video ─────────────────────────────────────────────── --}}
<x-admin.modal-form id="modalVideo" title="Tambah Video"
                    :action="route('admin.ngobrol-statistik.store')" size="modal-lg">
    <input type="hidden" name="kategori" value="video">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Link YouTube</label>
            <input type="text" name="youtube_url" value="{{ old('youtube_url') }}" class="form-control" required
                   placeholder="https://www.youtube.com/watch?v=xxxxxxxxxxx">
            <div class="form-text">Bisa link watch, youtu.be, shorts, atau embed.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Deskripsi <span class="text-muted">(opsional)</span></label>
            <textarea name="deskripsi" rows="3" class="form-control">{{ old('deskripsi') }}</textarea>
        </div>
        @include('admin.ngobrol-statistik.urutan-status', ['catatan' => 'Urutan terkecil jadi video besar di halaman publik.'])
    </div>
</x-admin.modal-form>

{{-- ── Modal: Infografis ────────────────────────────────────────── --}}
<x-admin.modal-form id="modalInfografis" title="Tambah Infografis"
                    :action="route('admin.ngobrol-statistik.store')" size="modal-lg" upload>
    <input type="hidden" name="kategori" value="infografis">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Gambar Infografis</label>
            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp,image/gif" class="form-control">
            <div class="form-text">JPG, PNG, WEBP, atau GIF, maks. 5 MB. Saat edit, kosongkan untuk tetap memakai gambar lama.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Link Instagram <span class="text-muted">(opsional)</span></label>
            <input type="url" name="instagram_url" value="{{ old('instagram_url') }}" class="form-control"
                   placeholder="https://www.instagram.com/p/xxxxxxx/">
            <div class="form-text">Tautan unggahan infografis ini di Instagram. Muncul sebagai tombol Instagram di kartu publik.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Deskripsi <span class="text-muted">(opsional)</span></label>
            <textarea name="deskripsi" rows="3" class="form-control">{{ old('deskripsi') }}</textarea>
        </div>
        @include('admin.ngobrol-statistik.urutan-status', ['catatan' => 'Angka kecil tampil lebih dulu.'])
    </div>
</x-admin.modal-form>

{{-- ── Modal: Materi ────────────────────────────────────────────── --}}
<x-admin.modal-form id="modalMateri" title="Tambah Materi"
                    :action="route('admin.ngobrol-statistik.store')" size="modal-xl" upload>
    <input type="hidden" name="kategori" value="materi">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required
                   placeholder="mis. Apa itu Inflasi?">
        </div>
        <div class="col-12">
            <label class="form-label">Ringkasan <span class="text-muted">(opsional, tampil di kartu)</span></label>
            <textarea name="deskripsi" rows="2" class="form-control">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Isi Artikel</label>
            <textarea name="isi" rows="10" class="form-control" required>{{ old('isi') }}</textarea>
            <div class="form-text">Teks biasa. Pisahkan paragraf dengan satu baris kosong.</div>
        </div>
        <div class="col-md-8">
            <label class="form-label">Gambar Sampul <span class="text-muted">(opsional)</span></label>
            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp,image/gif" class="form-control">
            <div class="form-text">Maks. 5 MB. Saat edit, kosongkan untuk tetap memakai gambar lama.</div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <div class="form-check mt-3">
                <input type="checkbox" name="hapus_gambar" value="1" class="form-check-input" id="hapusGambarMateri">
                <label class="form-check-label" for="hapusGambarMateri">Hapus gambar sampul lama</label>
            </div>
        </div>
        @include('admin.ngobrol-statistik.urutan-status', ['catatan' => 'Angka kecil tampil lebih dulu.'])
    </div>
</x-admin.modal-form>
@endsection
