@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Kecamatan' : 'Tambah Kecamatan')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:520px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.kecamatan.update', $item) : route('admin.kecamatan.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Nama Kecamatan</label>
                <input type="text" name="nama_kecamatan" value="{{ old('nama_kecamatan', $item->nama_kecamatan) }}" class="form-control" required autofocus>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.kecamatan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
