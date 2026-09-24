<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class PerformaMenuController extends Controller
{
    public function index()
    {
        $mulai = now('Asia/Jakarta')->subDays(29)->startOfDay();

        // Mulai dari SEMUA menu (bukan dari transaksi), biar menu yang belum
        // pernah kejual sama sekali tetap ikut kehitung sebagai "0 terjual"
        $performa = Menu::with('kategori')
            ->withSum(['pesananItems as terjual' => function ($q) use ($mulai) {
                $q->whereHas('pesanan', fn ($q2) => $q2->where('status', 'selesai')->where('created_at', '>=', $mulai));
            }], 'jumlah')
            ->get()
            ->map(function ($menu) {
                $menu->terjual = (int) $menu->terjual;
                $menu->pendapatan = $menu->terjual * $menu->harga;
                return $menu;
            });

        $terlaris = $performa->sortByDesc('terjual')->take(5);
        $kurangLaris = $performa->sortBy('terjual')->take(5);

        $kategoriFavorit = $performa
            ->groupBy(fn ($m) => $m->kategori->nama ?? 'Tanpa Kategori')
            ->map(fn ($grup) => $grup->sum('terjual'))
            ->sortDesc();

        return view('owner.performa-menu', compact('terlaris', 'kurangLaris', 'kategoriFavorit'));
    }
}