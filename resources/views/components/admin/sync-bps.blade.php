@props([
    // Kunci modul di App\Services\Statistik\SinkronisasiBps, mis. 'kemiskinan'.
    'modul',
    // Keterangan singkat apa yang akan ditarik, ditampilkan di dialog konfirmasi.
    'isi' => 'data modul ini',
])

{{--
    Tombol tarik-ulang data dari BPS WebAPI.

    Konfirmasinya menyebut secara eksplisit bahwa data lama TIDAK dihapus,
    karena itu pertanyaan pertama operator sebelum berani menekan tombol yang
    menyentuh seluruh tabel.
--}}
<form method="POST" action="{{ route('admin.sinkronisasi', $modul) }}" class="d-inline"
      onsubmit="return confirm('Tarik {{ $isi }} terbaru dari BPS?\n\nBaris yang cocok akan diperbarui dan yang belum ada ditambahkan. Data yang Anda isi manual tidak dihapus.\n\nProses ini menghubungi server BPS dan bisa memakan waktu sampai satu menit.');">
    @csrf
    <button class="btn btn-outline-info btn-sm" data-sync-bps>
        <i class="bi bi-cloud-download"></i> Sinkronkan BPS
    </button>
</form>

@once
@push('scripts')
<script>
// Sinkronisasi menghubungi BPS dan bisa berjalan puluhan detik tanpa tanda apa
// pun di layar. Tanpa penanda ini operator mengira tombolnya tidak berfungsi
// lalu menekannya berulang kali.
document.addEventListener('submit', function (e) {
    var form = e.target.closest('form[action*="/sinkronisasi/"]');
    if (!form || e.defaultPrevented) return;

    var tombol = form.querySelector('[data-sync-bps]');
    if (!tombol) return;

    tombol.disabled = true;
    tombol.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghubungi BPS…';
});
</script>
@endpush
@endonce
