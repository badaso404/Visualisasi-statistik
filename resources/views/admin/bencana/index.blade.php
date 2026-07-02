@extends('admin.layout.app')
@section('title', 'Kebencanaan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Data Bencana</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('statistik.bencana') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> Lihat publik</a>
        <a href="{{ route('admin.bencana.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th><th>Jenis</th><th>Lokasi</th><th>Kecamatan</th><th>Tahun</th>
                    <th>Kejadian</th><th>Korban</th><th>Terdampak</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tanggal_kejadian ? \Carbon\Carbon::parse($item->tanggal_kejadian)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->jenis_bencana }}</td>
                        <td>{{ $item->nama_lokasi }}</td>
                        <td>{{ $item->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ number_format($item->jumlah_kejadian) }}</td>
                        <td>{{ number_format($item->jumlah_korban) }}</td>
                        <td>{{ number_format($item->jumlah_terdampak) }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.bencana.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.bencana.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
