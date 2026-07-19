@extends('admin.layout.app')
@section('title', 'Kecamatan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Daftar Kecamatan</h5>
    <button class="btn btn-primary btn-sm"
            data-modal-form="#modalKecamatan"
            data-action="{{ route('admin.kecamatan.store') }}"
            data-title="Tambah Kecamatan">
        <i class="bi bi-plus-lg"></i> Tambah
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th>Nama Kecamatan</th>
                    <th>Data Terkait</th>
                    <th style="width:160px" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $item)
                    @php
                        $terhapus = $rincian[$item->id]['terhapus'];
                        $lepas    = $rincian[$item->id]['lepas'];
                        $total    = array_sum($terhapus);
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->nama_kecamatan }}</td>
                        <td>
                            @if ($total === 0)
                                <span class="text-muted small">tidak ada</span>
                            @else
                                <span class="badge bg-secondary" data-bs-toggle="tooltip"
                                      title="{{ collect($terhapus)->map(fn ($n, $l) => "$l: $n")->implode(' · ') }}">
                                    {{ $total }} baris di {{ count($terhapus) }} modul
                                </span>
                            @endif
                            @if ($lepas !== [])
                                <span class="badge bg-light text-dark border" data-bs-toggle="tooltip"
                                      title="Tetap tersimpan, hanya kehilangan kaitan kecamatan bila kecamatan dihapus">
                                    +{{ array_sum($lepas) }} bencana
                                </span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-modal-form="#modalKecamatan"
                                    data-action="{{ route('admin.kecamatan.update', $item) }}"
                                    data-method="PUT"
                                    data-title="Edit Kecamatan"
                                    data-fields="{{ json_encode($item->only(['nama_kecamatan'])) }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.kecamatan.destroy', $item) }}" method="POST" class="d-inline"
                                  data-konfirmasi-hapus="kecamatan {{ $item->nama_kecamatan }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" @disabled($total > 0)
                                        @if ($total > 0) data-bs-toggle="tooltip" title="Masih dipakai {{ $total }} baris data. Kosongkan dulu bila memang ingin dihapus." @endif>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-admin.modal-form id="modalKecamatan" title="Tambah Kecamatan" :action="route('admin.kecamatan.store')">
    <div>
        <label class="form-label">Nama Kecamatan</label>
        <input type="text" name="nama_kecamatan" value="{{ old('nama_kecamatan') }}" class="form-control" required>
    </div>
</x-admin.modal-form>
@endsection
