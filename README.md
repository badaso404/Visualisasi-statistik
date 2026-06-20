# 📊 Visualisasi Statistik Jakarta Barat

Project Laravel 10 untuk menampilkan data statistik Kota Administrasi Jakarta Barat dalam bentuk chart interaktif dan peta.

---

## 🛠️ Teknologi yang Digunakan

- **Laravel 10** — Backend framework
- **MySQL** — Database
- **ApexCharts** — Library chart interaktif
- **Leaflet.js** — Library peta interaktif
- **Bootstrap 5** — CSS framework
- **OpenStreetMap** — Tile map (gratis, tanpa API key)

---

## 📁 Struktur Project

```
visualisasi-statistik/
├── app/
│   ├── Models/                  # Model database
│   │   ├── Kecamatan.php
│   │   ├── DataGeografis.php
│   │   ├── LuasKecamatan.php
│   │   ├── DataIklim.php
│   │   ├── DataKependudukan.php
│   │   ├── PendudukKecamatan.php
│   │   ├── PendudukKelurahan.php
│   │   ├── DataPendidikan.php
│   │   ├── PendidikanKecamatan.php
│   │   ├── DataKesehatan.php
│   │   ├── TenagaKesehatanKecamatan.php
│   │   └── FasilitasKesehatanKecamatan.php
│   └── Http/
│       └── Controllers/
│           └── StatistikController.php  # Controller utama
├── database/
│   ├── migrations/              # Struktur tabel
│   └── seeders/                 # Data awal
│       ├── GeografisSeeder.php
│       ├── IklimSeeder.php
│       ├── KependudukanSeeder.php
│       ├── PendidikanSeeder.php
│       └── KesehatanSeeder.php
├── public/
│   └── assets/
│       └── geojson/
│           └── kecamatan.geojson    # Batas wilayah kecamatan
└── resources/
    └── views/
        ├── layouts/
        │   └── app.blade.php        # Layout utama
        └── statistik/               # Halaman per kategori
            ├── geografis.blade.php
            ├── iklim.blade.php
            ├── kependudukan.blade.php
            ├── pendidikan.blade.php
            └── kesehatan.blade.php
```

---

## ⚙️ Cara Setup Project (Dari Awal)

### 1. Clone atau Copy Project

```bash
cd ~/Herd
# copy folder project ke sini
```

### 2. Install Dependencies

```bash
cd visualisasi-statistik
composer install
```

### 3. Buat File .env

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visualisasi_statistik
DB_USERNAME=root
DB_PASSWORD=
```

> Buat database `visualisasi_statistik` terlebih dahulu di DBeaver atau DBngin.

### 5. Jalankan Migration

```bash
php artisan migrate
```

### 6. Jalankan Seeder (Isi Data Awal)

```bash
php artisan db:seed --class=GeografisSeeder
php artisan db:seed --class=IklimSeeder
php artisan db:seed --class=KependudukanSeeder
php artisan db:seed --class=PendidikanSeeder
php artisan db:seed --class=KesehatanSeeder
```

### 7. Akses di Browser

Kalau pakai Laravel Herd, langsung akses:
```
http://visualisasi-statistik.test
```

---

## 🗺️ Halaman yang Tersedia

| URL | Halaman | Isi |
|-----|---------|-----|
| `/statistik/geografis` | Geografis | Luas wilayah kecamatan, peta Jakarta Barat |
| `/statistik/iklim` | Iklim | Suhu, curah hujan, kelembaban per bulan |
| `/statistik/kependudukan` | Kependudukan | Populasi per kecamatan & kelurahan, peta sebaran |
| `/statistik/pendidikan` | Pendidikan | Jumlah murid, guru, APM & APK per kecamatan |
| `/statistik/kesehatan` | Kesehatan | Tenaga & fasilitas kesehatan per kecamatan |

---

## 🗄️ Struktur Database

### Tabel Master
- `kecamatan` — Daftar 8 kecamatan Jakarta Barat

### Tabel Geografis
- `data_geografis` — Luas kota & ketinggian per tahun
- `luas_kecamatan` — Luas & persentase per kecamatan per tahun

### Tabel Iklim
- `data_iklim` — Data iklim per bulan (suhu, hujan, angin, dll)

### Tabel Kependudukan
- `data_kependudukan` — Summary jumlah penduduk per tahun
- `penduduk_kecamatan` — Jumlah penduduk per kecamatan
- `penduduk_kelurahan` — Jumlah penduduk per kelurahan + koordinat peta

### Tabel Pendidikan
- `data_pendidikan` — APM & APK per tahun
- `pendidikan_kecamatan` — Jumlah murid, guru, sekolah per kecamatan

### Tabel Kesehatan
- `data_kesehatan` — Summary fasilitas kesehatan per tahun
- `tenaga_kesehatan_kecamatan` — Jumlah tenaga kesehatan per kecamatan
- `fasilitas_kesehatan_kecamatan` — Jumlah fasilitas kesehatan per kecamatan

---

## 📋 Pembagian Tugas Magang

Setiap halaman bisa dikerjakan secara terpisah. Berikut pembagian yang disarankan:

| Tugas | File yang Diubah | Keterangan |
|-------|-----------------|------------|
| Halaman Geografis | `views/statistik/geografis.blade.php` | Chart luas wilayah + peta |
| Halaman Iklim | `views/statistik/iklim.blade.php` | 6 chart data iklim per bulan |
| Halaman Kependudukan | `views/statistik/kependudukan.blade.php` | Chart + peta sebaran penduduk |
| Halaman Pendidikan | `views/statistik/pendidikan.blade.php` | Chart murid, guru, sekolah |
| Halaman Kesehatan | `views/statistik/kesehatan.blade.php` | Chart tenaga & fasilitas kesehatan |

> **Catatan:** Jangan ubah file Controller dan Model tanpa koordinasi dengan tech lead.

---

## 🔧 Cara Update Data

### Tambah data baru via Seeder
Edit file seeder yang sesuai, lalu jalankan ulang:
```bash
php artisan db:seed --class=NamaSeeder
```

### Tambah data langsung via SQL (DBeaver)
```sql
INSERT INTO nama_tabel (...) VALUES (...);
```

### Hapus data via SQL
```sql
DELETE FROM nama_tabel WHERE id = 1;
```

---

## 📚 Referensi Library

- [ApexCharts Docs](https://apexcharts.com/docs/)
- [Leaflet.js Docs](https://leafletjs.com/reference.html)
- [Laravel 10 Docs](https://laravel.com/docs/10.x)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)

---

## 📞 Kontak

Kalau ada pertanyaan WA gua langsung aja ya guys atau tanya langsung di microsoft teams aja ya.

> Terkait data ini hanya dummy
