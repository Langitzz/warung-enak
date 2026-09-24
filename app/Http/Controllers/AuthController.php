<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Halaman form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Halaman form daftar (pelanggan)
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses daftar. Role dipaksa 'pelanggan' dari sini, bukan dari input
    // form, supaya tidak bisa diutak-atik jadi admin/kasir lewat form daftar.
    public function register(Request $request)
    {
        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email ini sudah terdaftar.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]
        );

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'pelanggan',
            'aktif' => true,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect($this->tujuan($user));
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ]
        );

        // Batasi percobaan login: maksimal 5 kali per kombinasi email dan alamat IP,
        // supaya password tidak bisa ditebak dengan mencoba berkali-kali.
        $kunci = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
            ]);
        }

        // Akun harus aktif. Pesan gagalnya sengaja dibuat sama untuk semua sebab
        // (email salah, password salah, akun nonaktif), supaya orang luar tidak
        // bisa mengetahui email mana yang terdaftar.
        $berhasil = Auth::attempt(
            array_merge($credentials, ['aktif' => true]),
            $request->boolean('ingat')
        );

        if (! $berhasil) {
            RateLimiter::hit($kunci);

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah, atau akun tidak aktif.',
            ]);
        }

        RateLimiter::clear($kunci);

        // Ganti ID sesi setelah login (mencegah pembajakan sesi)
        $request->session()->regenerate();

        return redirect($this->tujuan($request->user()));
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus sesi dan buat token CSRF baru
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Semua role (admin, kasir, pelanggan) diarahkan ke landing page
        return redirect('/');
    }

    // Tujuan setelah login, tergantung role
    private function tujuan(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($user->isKasir()) {
            return route('kasir.index');
        }

        if ($user->role === 'chef') {
            return route('chef.dashboard');
        }

        if ($user->role === 'supervisor') {
            return route('supervisor.dashboard');
        }

        if ($user->role === 'owner') {
            return route('owner.dashboard');
        }

        // Pelanggan ke landing page.
        return url('/');
    }
}
