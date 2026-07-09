<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan penting: GeografisSeeder membuat data Kecamatan (master),
        // seeder lain hanya mencari kecamatan berdasarkan nama, jadi harus setelahnya.
        $this->call([
            GeografisSeeder::class,    // membuat Kecamatan + data geografis + luas
            IklimSeeder::class,
            KependudukanSeeder::class,
            PendidikanSeeder::class,
            KesehatanSeeder::class,
            BencanaSeeder::class,      // butuh Kecamatan sudah ada
            TitikBencanaSeeder::class, // titik POI peta bencana (butuh Kecamatan)
            InfrastrukturDigitalSeeder::class, // JakWiFi & CCTV per kecamatan (butuh Kecamatan)
            KemiskinanSeeder::class,   // ringkasan + per kecamatan (butuh Kecamatan)
            AdminSeeder::class,        // akun admin (login panel)
        ]);
    }
}
