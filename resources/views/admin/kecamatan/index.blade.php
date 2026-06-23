@extends('admin.layout.app')
@section('title', 'Kecamatan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Daftar Kecamatan</h5>
    <a href="{{ route('admin.kecamatan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th style="width:60px">#</th><th>Nama Kecamatan</th><th style="width:160px" class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->nama_kecamatan }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.kecamatan.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.kecamatan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kecamatan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
