@props([
    'prefix',            // awalan nama route, mis. 'admin.penduduk-kecamatan'
    'judul' => 'Data',   // dipakai di judul modal import
    // Kunci pencocokan baris, ditampilkan di keterangan modal. Bawaannya kunci
    // tabel per-kecamatan karena itu yang dipakai mayoritas modul.
    'kunci' => 'kecamatan + tahun',
])

@php
    $idModal = 'modalImport' . \Illuminate\Support\Str::studly(str_replace('.', '-', $prefix));
@endphp

<a href="{{ route($prefix . '.export') }}" class="btn btn-outline-success btn-sm">
    <i class="bi bi-download"></i> Export CSV
</a>
<a href="{{ route($prefix . '.template') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-file-earmark-arrow-down"></i> Template
</a>
<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#{{ $idModal }}">
    <i class="bi bi-upload"></i> Import CSV
</button>

<div class="modal fade" id="{{ $idModal }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route($prefix . '.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title">Import {{ $judul }} (CSV)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pilih file CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" class="form-control" required>
                </div>
                <div class="small text-muted">
                    Baris dicocokkan per <b>{{ $kunci }}</b> — data yang sudah ada akan
                    <b>diperbarui</b>, bukan diduplikat. Kolom yang dikosongkan dibiarkan apa adanya.
                    Belum punya format? <a href="{{ route($prefix . '.template') }}">Unduh template</a>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Import</button>
            </div>
        </form>
    </div>
</div>
