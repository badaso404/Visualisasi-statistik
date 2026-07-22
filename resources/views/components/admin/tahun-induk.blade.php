@props([
    // Koleksi record induk (biasanya $items) atau koleksi tahun.
    'induk',
    // Sebutan ringkasan induk untuk pesan bila belum ada satu tahun pun.
    'sebutan' => 'data ringkasan',
    'tab'     => 'tab Ringkasan',
])

@php
    // Menerima koleksi model maupun koleksi tahun mentah, supaya pemanggil tidak
    // perlu tahu bentuk mana yang kebetulan tersedia di view-nya.
    $daftarTahun = collect($induk)
        ->map(fn ($t) => is_object($t) ? $t->tahun : $t)
        ->unique()->sortDesc()->values();
@endphp

{{--
    Tahun tabel anak dipilih dari tahun yang sudah punya ringkasan induk, bukan
    diketik bebas. Halaman publik menyusun daftar tahunnya dari tabel induk dan
    menolak tahun tanpa ringkasan, jadi tahun lain akan tersimpan tetapi tidak
    pernah bisa dibuka pengunjung.
--}}
<label class="form-label">Tahun</label>
<select name="tahun" class="form-select" required @disabled($daftarTahun->isEmpty())>
    <option value="">— pilih —</option>
    @foreach ($daftarTahun as $t)
        <option value="{{ $t }}" @selected(old('tahun') == $t)>{{ $t }}</option>
    @endforeach
</select>

@if ($daftarTahun->isEmpty())
    <div class="form-text text-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Belum ada {{ $sebutan }}. Tambahkan lewat {{ $tab }} lebih dulu.
    </div>
@else
    <div class="form-text">Hanya tahun yang sudah punya {{ $sebutan }}.</div>
@endif
