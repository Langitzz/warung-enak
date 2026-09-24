<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Chef\DashboardController as ChefDashboardController;
use App\Http\Controllers\Chef\MenuController as ChefMenuController;
use App\Http\Controllers\Chef\PesananController as ChefPesananController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LaporanBisnisController;
use App\Http\Controllers\Owner\PerformaMenuController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\PesananController as SupervisorPesananController;
use Illuminate\Support\Facades\Route;

// =====================================================================
// USER / LANDING PAGE
// =====================================================================
Route::get('/', function () {
    return view('user.landing');
});

// =====================================================================
// AUTH (login dan logout, dipakai semua role)
// =====================================================================
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register.store');

// ================= KASIR =================
Route::prefix('kasir')
    ->middleware(['auth', 'role:kasir,admin'])
    ->name('kasir.')
    ->group(function () {
        Route::get('/', [KasirController::class, 'index'])
            ->name('index');
        Route::post('/bayar', [KasirController::class, 'bayar'])
            ->name('bayar');
    });

// ================= CHEF =================
Route::prefix('chef')
    ->middleware(['auth', 'role:chef'])
    ->name('chef.')
    ->group(function () {
        Route::get('/dashboard', [ChefDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/pesanan', [ChefPesananController::class, 'index'])
            ->name('pesanan.index');
        Route::patch('/pesanan/{pesanan}/status', [ChefPesananController::class, 'updateStatus'])
            ->name('pesanan.status');

        Route::get('/menu', [ChefMenuController::class, 'index'])
            ->name('menu.index');

        Route::get('/riwayat', [ChefPesananController::class, 'riwayat'])
            ->name('riwayat');
    });

// ================= SUPERVISOR =================
Route::prefix('supervisor')
    ->middleware(['auth', 'role:supervisor'])
    ->name('supervisor.')
    ->group(function () {
        Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/pesanan', [SupervisorPesananController::class, 'index'])
            ->name('pesanan.index');

        // Reuse langsung halaman Laporan Penjualan admin (datanya sama, cuma beda sidebar/akses)
        Route::get('/laporan', [PesananController::class, 'laporanPenjualan'])
            ->name('laporan');

        // Belum ada sistem activity log, sesuai instruksi brief dipakai halaman pengganti dulu
        Route::view('/aktivitas', 'admin.segera', ['judul' => 'Aktivitas Operasional'])
            ->name('aktivitas');
    });

// ================= OWNER =================
Route::prefix('owner')
    ->middleware(['auth', 'role:owner'])
    ->name('owner.')
    ->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])
            ->name('dashboard');

        // Reuse langsung halaman Laporan Penjualan admin, sama kayak Supervisor
        Route::get('/laporan', [PesananController::class, 'laporanPenjualan'])
            ->name('laporan');
        Route::get('/performa-menu', [PerformaMenuController::class, 'index'])
            ->name('performa-menu');
        Route::get('/laporan-bisnis', [LaporanBisnisController::class, 'index'])
            ->name('laporan-bisnis');
    });

// ================= PEMESANAN (menu & keranjang) =================
Route::get('/menu', [PemesananController::class, 'menu'])
    ->name('menu.index');
Route::post('/keranjang/{menu}', [PemesananController::class, 'tambahKeranjang'])
    ->name('keranjang.tambah');
Route::get('/keranjang', [PemesananController::class, 'keranjang'])
    ->name('keranjang.index');
Route::patch('/keranjang/{menu}', [PemesananController::class, 'ubahKeranjang'])
    ->name('keranjang.ubah');
Route::delete('/keranjang/{menu}', [PemesananController::class, 'hapusKeranjang'])
    ->name('keranjang.hapus');

Route::get('/checkout', [PemesananController::class, 'checkout'])
    ->name('checkout.index');
Route::post('/checkout', [PemesananController::class, 'prosesCheckout'])
    ->name('checkout.store');

Route::get('/pesanan/{kode}', [PemesananController::class, 'konfirmasi'])
    ->name('pesanan.konfirmasi');
Route::post('/pesanan/{kode}/batalkan', [PemesananController::class, 'batalkan'])
    ->name('pesanan.batalkan');
Route::get('/cek-pesanan', [PemesananController::class, 'formCekPesanan'])
    ->name('pesanan.cek');
Route::post('/cek-pesanan', [PemesananController::class, 'cekPesanan'])
    ->middleware('throttle:6,1')
    ->name('pesanan.cek.proses');
Route::get('/pesanan-saya', [PemesananController::class, 'riwayatSaya'])
    ->middleware('auth')
    ->name('pesanan.saya');

// =====================================================================
// AKUN (profil, untuk semua role yang sudah login)
// =====================================================================
Route::middleware('auth')
    ->group(function () {
        Route::get('/profil', [ProfilController::class, 'edit'])
            ->name('profil.edit');
        Route::patch('/profil', [ProfilController::class, 'update'])
            ->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'updatePassword'])
            ->name('profil.password');
    });

// =====================================================================
// ADMIN (hanya untuk role admin yang sudah login)
// =====================================================================
Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::resource('menu', MenuController::class)
            ->except('show');

        // ---------------------------------------------------------------
        // ROUTE PENGGANTI: halamannya belum dibuat, cuma menampilkan admin/segera.
        // BELUM ADA BACKEND: hapus/ganti route ini kalau fitur aslinya sudah dikerjakan.
        // ---------------------------------------------------------------
        Route::resource('kategori', KategoriController::class)
            ->except('show');

        Route::resource('pengeluaran', PengeluaranController::class)
            ->except('show');

        Route::resource('pesanan', PesananController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::patch('pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])
            ->name('pesanan.status');

        Route::get('laporan/penjualan', [PesananController::class, 'laporanPenjualan'])
            ->name('laporan.penjualan');

        Route::get('laporan/riwayat', [PesananController::class, 'riwayat'])
            ->name('laporan.riwayat');

        Route::resource('pengguna', PenggunaController::class)
            ->except('show');

        Route::get('pengguna/role', [RoleController::class, 'index'])
            ->name('pengguna.role');

        Route::get('pengaturan/profil', [PengaturanController::class, 'profil'])
            ->name('pengaturan.profil');
        Route::put('pengaturan/profil', [PengaturanController::class, 'updateProfil'])
            ->name('pengaturan.profil.update');
        Route::get('pengaturan/sistem', [PengaturanController::class, 'sistem'])
            ->name('pengaturan.sistem');
        Route::put('pengaturan/sistem', [PengaturanController::class, 'updateSistem'])
            ->name('pengaturan.sistem.update');
    });
