<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\PesananItem;

class LaporanBisnisController extends Controller
{
    public function index()
    {
        $totalPendapatan = (int) Pesanan::where('status', 'selesai')->sum('total');
        $totalTransaksi = Pesanan::count();
        $totalSelesai = Pesanan::where('status', 'selesai')->count();
        $jumlahMenu = Menu::count();
        $menuTersedia = Menu::where('tersedia', true)->count();

        // Menu terlaris sepanjang waktu (beda dari Dashboard yang cuma 30 hari)
        $menuTerlaris = PesananItem::whereNotNull('menu_id')
            ->whereHas('pesanan', fn ($q) => $q->where('status', 'selesai'))
            ->selectRaw('menu_id, SUM(jumlah) as total_terjual')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->with('menu')
            ->get();

        return view('owner.laporan-bisnis', compact(
            'totalPendapatan', 'totalTransaksi', 'totalSelesai', 'jumlahMenu', 'menuTersedia', 'menuTerlaris'
        ));
    }
}