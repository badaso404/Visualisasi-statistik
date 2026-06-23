@extends('admin.layout.app')
@section('title', 'Pendidikan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Pendidikan (ringkasan APM/APK per tahun)</h5>
    <a href="{{ route('admin.pendidikan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tahun</th>
                    <th>APM SD/MI</th><th>APM SMP/MTs</th><th>APM SMA/SMK</th>
                    <th>APK SD/MI</th><th>APK SMP/MTs</th><th>APK SMA/SMK</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ $item->apm_sd_mi }}</td>
                        <td>{{ $item->apm_smp_mts }}</td>
                        <td>{{ $item->apm_sma_smk_man }}</td>
                        <td>{{ $item->apk_sd_mi }}</td>
                        <td>{{ $item->apk_smp_mts }}</td>
                        <td>{{ $item->apk_sma_smk_man }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.pendidikan.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.pendidikan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Pendidikan per Kecamatan</h6>
    <a href="{{ route('admin.pendidikan-kecamatan.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kecamatan</th><th>Tahun</th><th>Murid</th><th>Guru</th><th>Sekolah</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($perKecamatan as $row)
                    <tr>
                        <td>{{ $row->kecamatan->nama_kecamatan ?? '-' }}</td>
                        <td>{{ $row->tahun }}</td>
                        <td>{{ number_format($row->jumlah_murid, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->jumlah_guru, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->jumlah_sekolah, 0, ',', '.') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.pendidikan-kecamatan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.pendidikan-kecamatan.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
