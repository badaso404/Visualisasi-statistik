<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    
    protected $fillable = ['nama_kecamatan'];

    public function luasKecamatan()
    {
        return $this->hasMany(LuasKecamatan::class);
    }

    /**
     * Tabel yang foreign key-nya cascadeOnDelete: barisnya IKUT TERHAPUS untuk
     * semua tahun begitu kecamatan dihapus, dan tidak bisa dibatalkan.
     *
     * @var array<string, class-string<Model>>  label => model
     */
    public const IKUT_TERHAPUS = [
        'Penduduk per kecamatan'   => PendudukKecamatan::class,
        'Penduduk per kelurahan'   => PendudukKelurahan::class,
        'Pendidikan per kecamatan' => PendidikanKecamatan::class,
        'Tenaga kesehatan'         => TenagaKesehatanKecamatan::class,
        'Fasilitas kesehatan'      => FasilitasKesehatanKecamatan::class,
        'Kemiskinan per kecamatan' => KemiskinanKecamatan::class,
        'JakWiFi'                  => JakWifiKecamatan::class,
        'CCTV'                     => CctvKecamatan::class,
        'Luas kecamatan'           => LuasKecamatan::class,
    ];

    /**
     * Tabel dengan nullOnDelete: barisnya tetap ada, hanya kehilangan kaitan
     * kecamatan (kolom kecamatan_id jadi kosong).
     *
     * @var array<string, class-string<Model>>
     */
    public const KEHILANGAN_KAITAN = [
        'Data bencana'  => DataBencana::class,
        'Titik bencana' => TitikBencana::class,
    ];

    /** @return array<string, int> label => jumlah baris, hanya yang > 0 */
    private function hitung(array $daftar): array
    {
        $rincian = [];

        foreach ($daftar as $label => $model) {
            $jumlah = $model::where('kecamatan_id', $this->id)->count();
            if ($jumlah > 0) {
                $rincian[$label] = $jumlah;
            }
        }

        return $rincian;
    }

    /** @return array<string, int> data yang akan lenyap permanen */
    public function rincianIkutTerhapus(): array
    {
        return $this->hitung(self::IKUT_TERHAPUS);
    }

    /** @return array<string, int> data yang bertahan tapi kehilangan kaitan kecamatan */
    public function rincianKehilanganKaitan(): array
    {
        return $this->hitung(self::KEHILANGAN_KAITAN);
    }

    public function totalIkutTerhapus(): int
    {
        return array_sum($this->rincianIkutTerhapus());
    }
}