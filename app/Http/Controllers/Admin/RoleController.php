<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class RoleController extends Controller
{
    // Daftar role beserta hak aksesnya. Ini hanya PENJELASAN untuk ditampilkan.
    // Aturan akses yang sebenarnya dijaga oleh middleware 'role' di routes/web.php.
    //
    // 'siap' => true  : hak akses ini sudah berfungsi di sistem.
    // 'siap' => false : fiturnya belum dibuat (ditampilkan dengan tanda "Segera").
    // Kalau fiturnya sudah selesai, ubah 'siap' jadi true di sini.
    private const ROLE = [
        'admin' => [
            'nama' => 'Admin',
            'deskripsi' => 'Pengelola warung dengan akses penuh ke panel admin.',
            'akses' => [
                ['teks' => 'Mengelola menu dan kategori menu', 'siap' => true],
                ['teks' => 'Mengelola akun pengguna', 'siap' => true],
                ['teks' => 'Mengatur profil warung dan pengaturan sistem', 'siap' => true],
                ['teks' => 'Mengelola pesanan dan melihat laporan penjualan', 'siap' => true],
                ['teks' => 'Mencatat pengeluaran dan melihat laba bersih', 'siap' => true],
                ['teks' => 'Mengelola profil sendiri dan mengganti password', 'siap' => true],
            ],
            'batasan' => [],
        ],
        'kasir' => [
            'nama' => 'Kasir',
            'deskripsi' => 'Staf yang melayani pesanan langsung di warung.',
            'akses' => [
                ['teks' => 'Membuat dan memproses pesanan lewat halaman kasir', 'siap' => true],
                ['teks' => 'Mengelola profil sendiri dan mengganti password', 'siap' => true],
            ],
            'batasan' => [
                'Tidak bisa membuka panel admin (menu, pengguna, pengaturan, laporan).',
            ],
        ],
        'chef' => [
            'nama' => 'Chef',
            'deskripsi' => 'Bertanggung jawab memproses pesanan di dapur.',
            'akses' => [
                ['teks' => 'Melihat dashboard dan antrean pesanan dapur', 'siap' => true],
                ['teks' => 'Mengubah status pesanan (Diproses, Siap)', 'siap' => true],
                ['teks' => 'Melihat daftar menu (hanya lihat)', 'siap' => true],
                ['teks' => 'Melihat riwayat masakan yang sudah selesai', 'siap' => true],
                ['teks' => 'Mengelola profil sendiri dan mengganti password', 'siap' => true],
            ],
            'batasan' => [
                'Tidak bisa mengubah data menu, harga, pengguna, atau pengaturan sistem.',
                'Tidak bisa menandai pesanan Selesai atau Dibatalkan.',
            ],
        ],
        'supervisor' => [
            'nama' => 'Supervisor',
            'deskripsi' => 'Mengawasi jalannya operasional harian warung.',
            'akses' => [
                ['teks' => 'Melihat dashboard kondisi operasional', 'siap' => true],
                ['teks' => 'Memantau seluruh pesanan (hanya lihat)', 'siap' => true],
                ['teks' => 'Melihat Laporan Penjualan', 'siap' => true],
                ['teks' => 'Melihat aktivitas operasional', 'siap' => false],
                ['teks' => 'Mengelola profil sendiri dan mengganti password', 'siap' => true],
            ],
            'batasan' => [
                'Tidak bisa mengedit atau menghapus data pesanan/menu.',
                'Tidak bisa membuka Data Pengguna atau Pengaturan Sistem.',
            ],
        ],
        'owner' => [
            'nama' => 'Owner',
            'deskripsi' => 'Melihat performa dan kondisi bisnis warung secara keseluruhan.',
            'akses' => [
                ['teks' => 'Melihat dashboard performa bisnis', 'siap' => true],
                ['teks' => 'Melihat Ringkasan Penjualan', 'siap' => true],
                ['teks' => 'Melihat Performa Menu (terlaris & kurang laris)', 'siap' => true],
                ['teks' => 'Melihat dan mencetak Laporan Bisnis', 'siap' => true],
                ['teks' => 'Mengelola profil sendiri dan mengganti password', 'siap' => true],
            ],
            'batasan' => [
                'Tidak mengelola teknis sistem (bukan Admin).',
                'Tidak bisa membuka Data Pengguna, Role, atau Pengaturan Sistem.',
            ],
        ],
        'pelanggan' => [
            'nama' => 'Pelanggan',
            'deskripsi' => 'Pembeli yang mendaftar akun untuk memesan dengan lebih mudah.',
            'akses' => [
                ['teks' => 'Memesan menu dengan data yang tersimpan otomatis', 'siap' => true],
                ['teks' => 'Melihat riwayat pesanan sendiri', 'siap' => true],
                ['teks' => 'Membatalkan pesanan sendiri (selagi belum diproses lebih lanjut)', 'siap' => true],
                ['teks' => 'Mengelola profil sendiri dan mengganti password', 'siap' => true],
            ],
            'batasan' => [
                'Tidak bisa membuka panel admin maupun halaman kasir.',
                'Pembeli tanpa akun tetap bisa memesan dengan mengisi nama dan nomor WhatsApp.',
            ],
        ],
    ];

    // Halaman Role/Akses (hanya untuk dibaca)
    public function index()
    {
        // Jumlah pengguna per role: total dan yang aktif (dihitung asli dari database)
        $jumlah = User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $jumlahAktif = User::where('aktif', true)
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.role', [
            'roles' => self::ROLE,
            'jumlah' => $jumlah,
            'jumlahAktif' => $jumlahAktif,
        ]);
    }
}
