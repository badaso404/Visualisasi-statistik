<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->pastikanDatabaseUji($app);

        return $app;
    }

    /**
     * RefreshDatabase MENGHAPUS SEMUA TABEL pada koneksi yang aktif.
     *
     * Pemeriksaan ini sengaja diletakkan di createApplication(), bukan di
     * setUp() TestCase: RefreshDatabase berjalan di dalam parent::setUp(),
     * jadi penjaga yang dipasang setelahnya baru bersuara ketika tabel sudah
     * terlanjur dibuang. Di sini penjaga berjalan sebelum satu pun query
     * dijalankan, sehingga benar-benar mencegah, bukan sekadar melaporkan.
     */
    private function pastikanDatabaseUji(Application $app): void
    {
        $koneksi  = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$koneksi}.database");

        $aman = $database === ':memory:'
            || str_ends_with((string) $database, '_test')
            || str_ends_with((string) $database, '_testing');

        if (!$aman) {
            throw new RuntimeException(
                "Test dibatalkan: '{$database}' bukan database uji.\n" .
                "Test menghapus seluruh tabel pada database yang aktif, jadi namanya wajib\n" .
                "berakhiran _test / _testing (atau sqlite :memory:). Periksa DB_DATABASE\n" .
                "di phpunit.xml, dan jangan menimpanya lewat variabel lingkungan."
            );
        }
    }
}
