<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PesananItem;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = now('Asia/Jakarta')->startOfDay();
        $awalBulan = now('Asia/Jakarta')->startOfMonth();

        $pendapatanHariIni = (int) Pesanan::whereDate('created_at', $hariIni)
            ->where('status', 'selesai')->sum('total');
        $pendapatanBulanIni = (int) Pesanan::where('created_at', '>=', $awalBulan)
            ->where('status', 'selesai')->sum('total');
        $totalTransaksi = Pesanan::count();
        $totalSelesai = Pesanan::where('status', 'selesai')->count();

        // Menu terlaris 30 hari terakhir (pola sama kayak Dashboard admin)
        $mulai30 = now('Asia/Jakarta')->subDays(29)->startOfDay();
        $menuTerlaris = PesananItem::whereNotNull('menu_id')
            ->whereHas('pesanan', fn ($q) => $q->where('status', 'selesai')->where('created_at', '>=', $mulai30))
            ->selectRaw('menu_id, SUM(jumlah) as total_terjual')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->with('menu')
            ->get();

        // Grafik pendapatan 7 hari terakhir
        $mulaiGrafik = now('Asia/Jakarta')->subDays(6)->startOfDay();
        $dataPerHari = Pesanan::selectRaw('DATE(created_at) as tanggal, SUM(total) as pendapatan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$mulaiGrafik, now('Asia/Jakarta')->endOfDay()])
            ->groupBy('tanggal')->get()->keyBy('tanggal');

        $grafikLabels = [];
        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = now('Asia/Jakarta')->subDays($i);
            $grafikLabels[] = $tgl->locale('id')->translatedFormat('D');
            $grafikData[] = (int) ($dataPerHari->get($tgl->format('Y-m-d'))->pendapatan ?? 0);
        }

        return view('owner.dashboard', compact(
            'pendapatanHariIni', 'pendapatanBulanIni', 'totalTransaksi', 'totalSelesai',
            'menuTerlaris', 'grafikLabels', 'grafikData'
        ));
    }
}