@extends('admin.layout.app')
@section('title', 'Kemiskinan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Kemiskinan (ringkasan per tahun)</h5>
    <a href="{{ route('admin.kemiskinan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tahun</th>
                    <th>Penduduk Miskin</th><th>Persentase</th><th>Garis Kemiskinan</th>
                    <th>P1 (Kedalaman)</th><th>P2 (Keparahan)</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ number_format($item->jumlah_penduduk_miskin, 0, ',', '.') }} jiwa</td>
                        <td>{{ $item->persentase_penduduk_miskin }}%</td>
                        <td>Rp {{ number_format($item->garis_kemiskinan, 0, ',', '.') }}</td>
                        <td>{{ $item->indeks_kedalaman }}</td>
                        <td>{{ $item->indeks_keparahan }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.kemiskinan.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.kemiskinan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Kemiskinan per Kecamatan</h6>
    <a href="{{ route('admin.kemiskinan-kecamatan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kecamatan</th><th>Tahun</th>
                    <th>Penduduk Miskin</th><th>Keluarga Miskin (KK)</th>
                    <th>Penerima Bantuan</th><th>Persentase</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($perKecamatan as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ number_format($row->jumlah_penduduk_miskin, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->jumlah_keluarga_miskin, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->penerima_bantuan, 0, ',', '.') }}</td>
                        <td>{{ $row->persentase }}%</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.kemiskinan-kecamatan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.kemiskinan-kecamatan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
