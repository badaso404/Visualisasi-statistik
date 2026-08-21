<?php

namespace App\Services\Statistik;

use App\Models\FasilitasUmum;
use App\Models\Kecamatan;
use App\Models\PendudukKelurahan;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Menarik daftar fasilitas umum dari API situs kecamatan Jakarta Barat dan
 * menyimpannya ke tabel fasilitas_umum (DB sebagai cermin API).
 *
 * Kenapa dicermin, bukan dipanggil langsung tiap halaman dibuka:
 *
 *  1. API-nya mengunci per_page di 10 dan mengabaikan parameter lain, jadi
 *     menampilkan 777 fasilitas berarti 78 permintaan keluar — untuk satu
 *     halaman grafik.
 *  2. Server sumber membalas 429 (Too Many Requests) kalau ditembak beruntun;
 *     ini terbukti saat pengambilan pertama. Halaman publik yang bergantung
 *     padanya akan kosong tepat saat ramai dikunjungi.
 *  3. Grafik butuh agregat per kecamatan, sedangkan API hanya memberi daftar
 *     per halaman — agregatnya tidak bisa dihitung tanpa menarik semuanya.
 *
 * Karena itu penarikan dijadikan tindakan admin yang sesekali dijalankan
 * (tombol "Sinkronkan"), dengan jeda antar-halaman dan percobaan ulang saat
 * kena 429. Sesudahnya semua query jalan di basis data sendiri.
 */
class FasilitasJakbarSync extends ApiClient
{
    private const BASE = 'https://barat.jakarta.go.id/kecamatan/api/v1/fasilitas/';

    /**
     * Jeda antar-halaman (mikrodetik). Server sumber mengumumkan batasnya lewat
     * header `x-ratelimit-limit: 60`, yaitu 60 permintaan per menit, dan
     * menegakkannya dengan 429. 1,1 detik menyisakan sedikit ruang di bawah
     * batas itu; lebih cepat sedikit saja (0,4 detik sempat dicoba) langsung
     * ditolak di tengah kategori.
     */
    private const JEDA = 1_100_000;

    /** Batas halaman per kategori — penjaga kalau API salah melaporkan total. */
    private const MAKS_HALAMAN = 200;

    private ?Collection $petaKecamatan = null;
    private ?Collection $petaKelurahan = null;

    /**
     * @param  string[]|null  $kategori  null = semua kategori yang dikenal
     * @return array{ditambah:int, diperbarui:int, tanpa_kecamatan:int, error:?string}
     */
    public function jalankan(?array $kategori = null): array
    {
        $hasil = ['ditambah' => 0, 'diperbarui' => 0, 'tanpa_kecamatan' => 0, 'error' => null];

        $daftar = $kategori
            ? array_values(array_intersect($kategori, array_keys(FasilitasUmum::KATEGORI)))
            : array_keys(FasilitasUmum::KATEGORI);

        if (!$daftar) {
            $hasil['error'] = 'Tidak ada kategori yang dikenal untuk disinkronkan.';
            return $hasil;
        }

        $gagal = [];

        foreach ($daftar as $kat) {
            try {
                $items = $this->tarikKategori($kat);
            } catch (\Throwable $e) {
                // Satu kategori gagal tidak boleh membatalkan yang lain: yang
                // sudah masuk tetap tersimpan, yang gagal dilaporkan namanya.
                $gagal[] = FasilitasUmum::KATEGORI[$kat] . ' (' . $this->ringkasGalat($e) . ')';
                continue;
            }

            foreach ($items as $item) {
                $this->simpan($kat, $item, $hasil);
            }
        }

        if ($gagal) {
            $hasil['error'] = 'Gagal menarik: ' . implode('; ', $gagal);
        }

        return $hasil;
    }

    /**
     * Menyusuri seluruh halaman satu kategori.
     *
     * @return array<int, array<string, mixed>>
     */
    private function tarikKategori(string $kategori): array
    {
        $items = [];
        $halaman = 1;

        do {
            // Jeda percobaan ulang sengaja panjang: kegagalan yang khas di sini
            // adalah 429, dan mencoba lagi setengah detik kemudian (bawaan
            // ApiClient) hanya menambah beban tanpa mengubah hasil.
            $resp = $this->http(20)->retry(3, 2000)->get(self::BASE . $kategori, ['page' => $halaman]);

            if (!$resp->ok()) {
                throw new \RuntimeException('API membalas status ' . $resp->status());
            }

            $body = $resp->json();
            $data = $body['data'] ?? null;

            if (!is_array($data)) {
                throw new \RuntimeException('Balasan API tidak berisi daftar data');
            }

            $items = array_merge($items, $data);

            $total   = (int) ($body['total'] ?? 0);
            $perPage = max(1, (int) ($body['per_page'] ?? 10));
            $selesai = $data === [] || $halaman * $perPage >= $total;

            $halaman++;
            if (!$selesai) {
                usleep(self::JEDA);
            }
        } while (!$selesai && $halaman <= self::MAKS_HALAMAN);

        return $items;
    }

    /**
     * Pesan singkat untuk ditempel di flash admin.
     *
     * Http::retry() melempar RequestException yang pesannya memuat seluruh
     * badan balasan — dan saat kena 429 badan itu berupa halaman HTML error
     * setinggi layar. Yang berguna cuma kodenya, jadi hanya itu yang diambil.
     */
    private function ringkasGalat(\Throwable $e): string
    {
        if ($e instanceof RequestException) {
            return $e->response->status() === 429
                ? 'ditolak sumber karena terlalu sering diminta (429), coba lagi beberapa menit lagi'
                : 'API membalas status ' . $e->response->status();
        }

        return Str::limit(preg_replace('/\s+/', ' ', $e->getMessage()) ?? '', 120);
    }

    /** @param array{ditambah:int, diperbarui:int, tanpa_kecamatan:int, error:?string} $hasil */
    private function simpan(string $kategori, array $item, array &$hasil): void
    {
        // Nilai dari API rutin membawa tab & spasi di ujungnya ("GOR Tambora\t").
        $nama = $this->bersihkan($item['nama_layanan'] ?? '');
        if ($nama === '') {
            return;
        }

        $alamat  = $this->bersihkan($item['alamat_layanan'] ?? '');
        $wilayah = $this->bersihkan($item['wilayah'] ?? '');

        [$kecamatanId, $kelurahan] = $this->petakanWilayah($wilayah, $alamat, $nama);
        if (!$kecamatanId) {
            $hasil['tanpa_kecamatan']++;
        }

        $sumberId = isset($item['id']) ? (int) $item['id'] : null;

        $rec = FasilitasUmum::updateOrCreate(
            ['kategori' => $kategori, 'sumber_id' => $sumberId],
            [
                'nama'         => $nama,
                'alamat'       => $alamat !== '' ? $alamat : null,
                'kecamatan_id' => $kecamatanId,
                'kelurahan'    => $kelurahan,
                'foto'         => $this->bersihkan($item['foto'] ?? '') ?: null,
                'sumber'       => 'Kecamatan Jakarta Barat',
            ]
        );

        $rec->wasRecentlyCreated ? $hasil['ditambah']++ : $hasil['diperbarui']++;
    }

    /**
     * Menentukan kecamatan sebuah fasilitas.
     *
     * Sumbernya tidak punya kolom kecamatan, jadi ditebak berlapis dari yang
     * paling tepercaya ke yang paling longgar. Kolom `wilayah` dari API adalah
     * akun pengunggahnya, jadi isinya bisa "Kecamatan Tambora", "Kelurahan
     * Tegal Alur", atau "Super Admin"; dua bentuk pertama sendirian sudah
     * menutup 96% data, sisanya dikorek dari alamat lalu dari nama fasilitas.
     *
     * Dari 776 fasilitas, tersisa satu pos pemadam kebakaran yang tidak
     * menyebut wilayah di mana pun. Barisnya tetap disimpan dengan kecamatan
     * kosong, bukan dibuang: total tingkat kota tetap benar, dan admin bisa
     * melengkapinya lewat panel.
     *
     * @return array{0: ?int, 1: ?string}  [kecamatan_id, nama kelurahan]
     */
    private function petakanWilayah(string $wilayah, string $alamat, string $nama): array
    {
        $kecamatan = $this->petaKecamatan();
        $kelurahan = $this->petaKelurahan();

        // 1. "Kecamatan X" pada kolom wilayah
        if (preg_match('/^kecamatan\s+(.+)$/i', $wilayah, $m)) {
            if ($id = $kecamatan->get($this->kunci($m[1]))) {
                return [$id, null];
            }
        }

        // 2. "Kelurahan Y" pada kolom wilayah — kelurahan menentukan kecamatan
        if (preg_match('/^kelurahan\s+(.+)$/i', $wilayah, $m)) {
            if ($kel = $kelurahan->get($this->kunci($m[1]))) {
                return [$kel['kecamatan_id'], $kel['nama']];
            }
        }

        // 3. "…, Kec. Kalideres, …" pada alamat. Dibandingkan dalam bentuk
        // sudah-dibersihkan ("keckalideres"), jadi titik dan spasi setelah
        // "Kec" tidak perlu ikut dicocokkan.
        $alamatKunci = $this->kunci($alamat);
        foreach ($kecamatan as $kunci => $id) {
            if (preg_match('/kec(?:amatan)?' . preg_quote($kunci, '/') . '/', $alamatKunci)) {
                return [$id, null];
            }
        }

        // 4. Nama kelurahan yang muncul di tengah alamat
        foreach ($kelurahan as $kunci => $kel) {
            if (str_contains($alamatKunci, $kunci)) {
                return [$kel['kecamatan_id'], $kel['nama']];
            }
        }

        // 5. Terakhir: wilayah yang disebut pada nama fasilitasnya sendiri
        // ("Sektor Kalideres", "GOR Tambora"). Sengaja dicoba paling belakang
        // karena paling mudah keliru — nama tempat bisa memuat nama wilayah
        // lain — tapi untuk pos pemadam kebakaran, yang alamatnya cuma nama
        // jalan tanpa wilayah, ini satu-satunya petunjuk yang tersedia.
        $namaKunci = $this->kunci($nama);
        foreach ($kecamatan as $kunci => $id) {
            if (str_contains($namaKunci, $kunci)) {
                return [$id, null];
            }
        }

        return [null, null];
    }

    /**
     * Kunci pembanding: huruf saja, huruf kecil. Menyatukan penulisan yang
     * berbeda-beda pada data sumber — "Taman Sari" vs "Tamansari", "Kb. Jeruk"
     * vs "Kebon Jeruk" (lihat alias di petaKecamatan()).
     */
    private function kunci(string $teks): string
    {
        return preg_replace('/[^a-z]/', '', strtolower($teks)) ?? '';
    }

    private function bersihkan(?string $nilai): string
    {
        // Termasuk tab & non-breaking space, dua-duanya muncul di data sumber.
        return trim(preg_replace('/\s+/u', ' ', (string) $nilai) ?? '');
    }

    /** @return Collection<string, int> kunci nama kecamatan => id */
    private function petaKecamatan(): Collection
    {
        if ($this->petaKecamatan !== null) {
            return $this->petaKecamatan;
        }

        $peta = Kecamatan::all()->mapWithKeys(
            fn (Kecamatan $k) => [$this->kunci($k->nama_kecamatan) => $k->id]
        );

        // Singkatan yang dipakai data sumber tapi tidak ada di tabel master.
        $alias = ['kbjeruk' => 'kebonjeruk', 'grogol' => 'grogolpetamburan'];
        foreach ($alias as $dari => $ke) {
            if ($peta->has($ke)) {
                $peta->put($dari, $peta->get($ke));
            }
        }

        return $this->petaKecamatan = $peta;
    }

    /**
     * Peta kelurahan => kecamatan, dipinjam dari tabel penduduk_kelurahan —
     * satu-satunya tempat kaitan kelurahan→kecamatan sudah terdata (56
     * kelurahan Jakarta Barat lengkap), jadi tidak perlu tabel master baru.
     *
     * @return Collection<string, array{kecamatan_id:int, nama:string}>
     */
    private function petaKelurahan(): Collection
    {
        if ($this->petaKelurahan !== null) {
            return $this->petaKelurahan;
        }

        // Satu kelurahan punya satu baris per tahun; yang terbaru dipakai
        // karena kaitannya ke kecamatan bisa saja berubah (pemekaran wilayah).
        return $this->petaKelurahan = PendudukKelurahan::orderBy('tahun')
            ->get(['nama_kelurahan', 'kecamatan_id'])
            ->mapWithKeys(fn (PendudukKelurahan $k) => [
                $this->kunci($k->nama_kelurahan) => [
                    'kecamatan_id' => $k->kecamatan_id,
                    'nama'         => $k->nama_kelurahan,
                ],
            ]);
    }

    /** Kalimat hasil untuk pesan flash di panel admin. */
    public function ringkas(array $hasil): string
    {
        $pesan = sprintf(
            'Sinkronisasi fasilitas umum selesai: %d ditambah, %d diperbarui.',
            $hasil['ditambah'],
            $hasil['diperbarui']
        );

        if ($hasil['tanpa_kecamatan'] > 0) {
            $pesan .= sprintf(
                ' %d fasilitas belum terpetakan ke kecamatan — lengkapi lewat tombol Ubah.',
                $hasil['tanpa_kecamatan']
            );
        }

        if ($hasil['error']) {
            $pesan .= ' ' . $hasil['error'] . '.';
        }

        return $pesan;
    }
}
