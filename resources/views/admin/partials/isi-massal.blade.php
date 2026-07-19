{{--
    Form isi massal per kecamatan, dipakai bersama oleh beberapa modul.
    Variabel yang diharapkan:
      $judul        - judul halaman
      $fields       - ['nama_kolom' => 'Label'] kolom yang diisi
      $kecamatan    - koleksi Kecamatan
      $existing     - koleksi data tahun terpilih, di-key by kecamatan_id
      $tahun        - tahun yang sedang dibuka
      $tahunAda     - daftar tahun yang sudah punya data
      $routeBatch   - nama route form (GET)
      $routeSimpan  - nama route simpan (POST)
      $routeKembali - nama route index modul
--}}
@extends('admin.layout.app')
@section('title', $judul)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h6 class="mb-0">{{ $judul }}</h6>
        <div class="text-muted small">Isi semua kecamatan untuk satu tahun sekaligus.</div>
    </div>
    <a href="{{ route($routeKembali) }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex align-items-end gap-2 flex-wrap">
        <div>
            <label class="form-label mb-1">Tahun</label>
            <input type="number" name="tahun" value="{{ $tahun }}" class="form-control" style="width:140px" required>
        </div>
        <button class="btn btn-outline-primary"><i class="bi bi-arrow-repeat"></i> Muat Tahun Ini</button>
        @if ($tahunAda->isNotEmpty())
            <div class="ms-auto small text-muted align-self-center">
                Tahun tersedia:
                @foreach ($tahunAda as $t)
                    <a href="{{ route($routeBatch, ['tahun' => $t]) }}"
                       class="badge text-decoration-none {{ $t == $tahun ? 'bg-primary' : 'bg-secondary' }}">{{ $t }}</a>
                @endforeach
            </div>
        @endif
    </div>
</form>

<form method="POST" action="{{ route($routeSimpan) }}">
    @csrf
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th style="min-width:160px">Kecamatan</th>
                        @foreach ($fields as $def)
                            <th style="min-width:150px">{{ $def['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kecamatan as $i => $k)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ $k->nama_kecamatan }}</td>
                            @foreach ($fields as $name => $def)
                                <td>
                                    <input type="number" min="0"
                                           @if ($def['desimal']) step="0.01" @endif
                                           name="data[{{ $k->id }}][{{ $name }}]"
                                           value="{{ old("data.{$k->id}.{$name}", optional($existing[$k->id] ?? null)->{$name}) }}"
                                           class="form-control" placeholder="—">
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($fields) + 2 }}" class="text-center text-muted py-4">
                            Belum ada kecamatan. <a href="{{ route('admin.kecamatan.index') }}">Tambah kecamatan dulu</a>.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($kecamatan->isNotEmpty())
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Semua</button>
            <a href="{{ route($routeKembali) }}" class="btn btn-light">Batal</a>
        </div>
        <div class="form-text mt-2">
            Baris yang seluruh kolomnya dikosongkan akan dilewati. Data tahun {{ $tahun }} yang sudah ada akan diperbarui, bukan diduplikat.
        </div>
    @endif
</form>
@endsection
