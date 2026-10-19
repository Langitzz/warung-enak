<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    // Read-only: pantau semua pesanan (apapun statusnya), tanpa kemampuan ubah/hapus
    public function index(Request $request)
    {
        $pesanans = Pesanan::withSum('items as jumlah_porsi', 'jumlah')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $kataKunci = $request->input('cari');
                $query->where(function ($query) use ($kataKunci) {
                    $query->where('nama_pelanggan', 'like', "%{$kataKunci}%")
                        ->orWhere('no_whatsapp', 'like', "%{$kataKunci}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('supervisor.pesanan.index', compact('pesanans'));
    }
}