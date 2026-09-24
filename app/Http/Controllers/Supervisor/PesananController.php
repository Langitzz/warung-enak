<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;

class PesananController extends Controller
{
    // Read-only: pantau semua pesanan (apapun statusnya), tanpa kemampuan ubah/hapus
    public function index()
    {
        $pesanans = Pesanan::withSum('items as jumlah_porsi', 'jumlah')
            ->latest()
            ->latest('id')
            ->paginate(20);

        return view('supervisor.pesanan.index', compact('pesanans'));
    }
}