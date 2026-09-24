<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = now('Asia/Jakarta')->startOfDay();

        $jumlah = [
            'menunggu' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'menunggu')->count(),
            'diproses' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'diproses')->count(),
            'siap' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'siap')->count(),
            'selesai' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'selesai')->count(),
        ];

        // Pesanan yang masih perlu dikerjakan dapur, terlama dulu (antrean kerja)
        $pesananTerbaru = Pesanan::with('items')
            ->whereIn('status', ['menunggu', 'diproses', 'siap'])
            ->oldest()
            ->oldest('id')
            ->take(5)
            ->get();

        return view('chef.dashboard', [
            'jumlah' => $jumlah,
            'pesananTerbaru' => $pesananTerbaru,
        ]);
    }
}