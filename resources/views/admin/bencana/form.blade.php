@extends('admin.layout.app')
@section('title', $item->exists ? 'Edit Data Bencana' : 'Tambah Data Bencana')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('admin.bencana.update', $item) : route('admin.bencana.store') }}">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Bencana</label>
                    <select name="jenis_bencana" class="form-select" required>
                        <option value="">— pilih —</option>
                        @foreach ($jenisList as $jenis)
                            <option value="{{ $jenis }}" @selected(old('jenis_bencana', $item->jenis_bencana) == $jenis)>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan <span class="text-muted">(opsional)</span></label>
                    <select name="kecamatan_id" class="form-select">
                        <option value="">— pilih —</option>
                        @foreach ($kecamatan as $k)
                            <option value="{{ $k->id }}" @selected(old('kecamatan_id', $item->kecamatan_id) == $k->id)>{{ $k->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Lokasi</label>
                    <input type="text" name="nama_lokasi" value="{{ old('nama_lokasi', $item->nama_lokasi) }}" class="form-control" placeholder="mis. Kel. Kapuk" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $item->tahun ?? date('Y')) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Kejadian <span class="text-muted">(opsional)</span></label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $item->tanggal_kejadian ? \Carbon\Carbon::parse($item->tanggal_kejadian)->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Latitude <span class="text-muted">(opsional)</span></label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $item->latitude) }}" class="form-control" placeholder="-6.15">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Longitude <span class="text-muted">(opsional)</span></label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $item->longitude) }}" class="form-control" placeholder="106.78">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Kejadian</label>
                    <input type="number" name="jumlah_kejadian" value="{{ old('jumlah_kejadian', $item->jumlah_kejadian ?? 1) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Korban</label>
                    <input type="number" name="jumlah_korban" value="{{ old('jumlah_korban', $item->jumlah_korban ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Terdampak</label>
                    <input type="number" name="jumlah_terdampak" value="{{ old('jumlah_terdampak', $item->jumlah_terdampak ?? 0) }}" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan <span class="text-muted">(opsional)</span></label>
                    <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Sumber <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="sumber" value="{{ old('sumber', $item->sumber) }}" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="{{ route('admin.bencana.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
