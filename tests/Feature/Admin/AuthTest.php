<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Alur masuk/keluar panel admin. Ini satu-satunya kontrol akses ke seluruh data
 * statistik, jadi diuji terpisah dari asap-tes halaman.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    private const SANDI = 'rahasia-panjang';

    private function admin(array $atribut = []): User
    {
        return User::factory()->create($atribut + [
            'email'    => 'operator@jakbar.go.id',
            'password' => bcrypt(self::SANDI),
        ]);
    }

    public function test_kredensial_benar_masuk_ke_dashboard(): void
    {
        $admin = $this->admin();

        $this->post(route('admin.login.attempt'), [
            'email'    => $admin->email,
            'password' => self::SANDI,
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_sandi_salah_ditolak_tanpa_membocorkan_email_terdaftar(): void
    {
        $admin = $this->admin();

        $response = $this->from(route('admin.login'))->post(route('admin.login.attempt'), [
            'email'    => $admin->email,
            'password' => 'sandi-keliru',
        ]);

        $response->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');
        $this->assertGuest();

        // Pesannya harus sama untuk email terdaftar maupun tidak, supaya panel
        // tidak bisa dipakai memeriksa alamat email mana yang punya akun.
        $this->assertSame(
            'Email atau password salah.',
            session('errors')->first('email'),
        );
    }

    public function test_email_tak_dikenal_memberi_pesan_yang_sama(): void
    {
        $this->from(route('admin.login'))->post(route('admin.login.attempt'), [
            'email'    => 'bukan-siapa-siapa@contoh.id',
            'password' => self::SANDI,
        ])->assertSessionHasErrors('email');

        $this->assertSame(
            'Email atau password salah.',
            session('errors')->first('email'),
        );
    }

    public function test_percobaan_login_dibatasi_setelah_lima_kali_gagal(): void
    {
        RateLimiter::clear('login');
        $admin = $this->admin();

        // Lima kali pertama ditolak biasa (redirect balik ke form login).
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.attempt'), [
                'email'    => $admin->email,
                'password' => 'sandi-keliru',
            ])->assertRedirect();
        }

        // Yang keenam dicegat middleware throttle, bukan lagi oleh AuthController.
        $this->post(route('admin.login.attempt'), [
            'email'    => $admin->email,
            'password' => 'sandi-keliru',
        ])->assertStatus(429);

        // Sandi yang benar pun ikut tertahan selama masa kunci — inilah yang
        // membuat tebak-tebakan sandi tidak lagi murah.
        $this->post(route('admin.login.attempt'), [
            'email'    => $admin->email,
            'password' => self::SANDI,
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_penguncian_tidak_menular_ke_akun_lain_di_ip_yang_sama(): void
    {
        RateLimiter::clear('login');
        $korban = $this->admin();
        $lain   = $this->admin(['email' => 'kepala@jakbar.go.id']);

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('admin.login.attempt'), [
                'email'    => $korban->email,
                'password' => 'sandi-keliru',
            ]);
        }

        // Batas per-email sudah habis, tetapi batas per-IP (20/menit) belum,
        // sehingga operator lain di kantor yang sama tetap bisa masuk.
        $this->post(route('admin.login.attempt'), [
            'email'    => $lain->email,
            'password' => self::SANDI,
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($lain);
    }

    public function test_pengguna_yang_sudah_masuk_tidak_melihat_form_login(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.login'))
            ->assertRedirect('/admin');
    }

    public function test_keluar_mengakhiri_sesi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_root_mengarah_ke_portal_statistik(): void
    {
        $this->get('/')->assertRedirect('/statistik');
    }
}
