# Visualisasi Statistik

Project ini menggunakan **Laravel 10** sebagai backend utama. Dokumentasi ini berisi langkah-langkah setup project dari awal sampai bisa dijalankan di lokal.

## Kebutuhan Sistem

Sebelum menjalankan project, pastikan laptop sudah memiliki:

* PHP minimal versi 8.1
* Composer
* MySQL / MariaDB
* Node.js dan NPM
* Git
* Web server lokal seperti Laragon, Herd, XAMPP, atau Laravel Artisan Serve

## Cara Setup Project

### 1. Clone Repository

```bash
git clone https://github.com/badaso404/Visualisasi-statistik.git
```

Masuk ke folder project:

```bash
cd Visualisasi-statistik
```

### 2. Install Dependency Laravel

Jalankan perintah berikut:

```bash
composer install
```

Jika terjadi error dependency, pastikan versi PHP sudah sesuai dengan kebutuhan Laravel 10.

### 3. Copy File Environment

Copy file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Untuk Windows CMD:

```bash
copy .env.example .env
```

### 4. Generate APP_KEY

Jalankan perintah:

```bash
php artisan key:generate
```

Perintah ini akan membuat `APP_KEY` otomatis di file `.env`.

### 5. Setting Database

Buka file `.env`, lalu sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan bagian berikut:

* `DB_DATABASE` dengan nama database lokal
* `DB_USERNAME` dengan username database
* `DB_PASSWORD` dengan password database jika ada

Contoh jika menggunakan Laragon atau Herd biasanya:

```env
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database

Buat database secara manual melalui DBeaver, phpMyAdmin, TablePlus, atau terminal MySQL.

Contoh nama database:

```sql
CREATE DATABASE visualisasi_statistik;
```

Pastikan nama database sama dengan yang ada di file `.env`.

### 7. Jalankan Migration

Jika project menggunakan migration Laravel, jalankan:

```bash
php artisan migrate
```

Jika project sudah menggunakan database hasil restore/dump, migration tidak wajib dijalankan selama struktur tabel sudah tersedia.

Untuk mengecek status migration:

```bash
php artisan migrate:status
```

### 8. Install Dependency Frontend

Jalankan:

```bash
npm install
```

Lalu jalankan salah satu perintah berikut:

Untuk development:

```bash
npm run dev
```

Untuk build production:

```bash
npm run build
```

### 9. Buat Storage Link

Jika project menggunakan upload file atau akses file dari folder storage, jalankan:

```bash
php artisan storage:link
```

Jika muncul pesan link sudah ada, berarti tidak perlu dibuat ulang.

### 10. Jalankan Project

Jalankan project Laravel:

```bash
php artisan serve
```

Project akan berjalan di:

```text
http://127.0.0.1:8000
```

## Perintah Cache Laravel

Jika ada perubahan config, route, atau view tetapi belum terbaca, jalankan:

```bash
php artisan optimize:clear
```

Atau jalankan satu per satu:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Struktur File Penting

Beberapa file dan folder penting dalam project:

```text
app/                 Berisi logic utama aplikasi Laravel
routes/web.php       Berisi route website
resources/views/     Berisi file Blade template
public/              Berisi asset publik seperti CSS, JS, gambar
database/migrations/ Berisi file migration database
.env                 Konfigurasi lokal aplikasi
```

## Catatan Penting

File berikut tidak perlu di-push ke GitHub:

```text
.env
vendor/
node_modules/
storage/logs/
```

Jika ada file besar seperti PDF, video, atau asset berukuran besar, sebaiknya tidak dimasukkan ke repository GitHub. File besar bisa disimpan di storage server, Google Drive, atau tempat penyimpanan lain.

## Troubleshooting

### Error `.env` belum ada

Jika muncul error karena file `.env` tidak ditemukan, jalankan:

```bash
cp .env.example .env
php artisan key:generate
```

### Error database tidak ditemukan

Pastikan database sudah dibuat dan konfigurasi `.env` sudah benar.

Cek kembali bagian ini:

```env
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

### Error `APP_KEY` kosong

Jalankan:

```bash
php artisan key:generate
```

### Error storage tidak tampil

Jalankan:

```bash
php artisan storage:link
```

### Error cache atau config lama masih terbaca

Jalankan:

```bash
php artisan optimize:clear
```

## Alur Setup Singkat

Jika ingin setup cepat, jalankan perintah berikut:

```bash
git clone https://github.com/badaso404/Visualisasi-statistik.git
cd Visualisasi-statistik
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev
php artisan storage:link
php artisan serve
```

Setelah itu, sesuaikan konfigurasi database di file `.env`, lalu jalankan migration atau restore database sesuai kebutuhan project.
