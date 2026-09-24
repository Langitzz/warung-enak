<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = now('Asia/Jakarta')->startOfDay();

        $pesananHariIni = Pesanan::whereDate('created_at', $hariIni)->count();
        $pendapatanHariIni = (int) Pesanan::whereDate('created_at', $hariIni)
            ->where('status', 'selesai')
            ->sum('total');

        $statusOperasional = [
            'menunggu' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'menunggu')->count(),
            'diproses' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'diproses')->count(),
            'siap' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'siap')->count(),
            'selesai' => Pesanan::whereDate('created_at', $hariIni)->where('status', 'selesai')->count(),
        ];

        return view('supervisor.dashboard', [
            'pesananHariIni' => $pesananHariIni,
            'pesananDiproses' => $statusOperasional['menunggu'] + $statusOperasional['diproses'] + $statusOperasional['siap'],
            'pesananSelesai' => $statusOperasional['selesai'],
            'pendapatanHariIni' => $pendapatanHariIni,
            'statusOperasional' => $statusOperasional,
        ]);
    }
}