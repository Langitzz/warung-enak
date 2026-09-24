<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    // Pesanan yang masih perlu dikerjakan dapur (belum selesai/dibatalkan), terlama dulu
    public function index()
    {
        $pesanans = Pesanan::with('items')
            ->whereIn('status', ['menunggu', 'diproses', 'siap'])
            ->oldest()
            ->oldest('id')
            ->paginate(15);

        return view('chef.pesanan.index', compact('pesanans'));
    }

    // Chef cuma boleh mindahin ke 'diproses' atau 'siap' — bukan 'selesai' (urusan penyerahan)
    // atau 'dibatalkan' (keputusan bisnis), meski alur pesanan (bolehKe) sebenarnya izinkan lebih.
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate(
            ['status' => ['required', 'in:diproses,siap']],
            ['status.required' => 'Status wajib dipilih.', 'status.in' => 'Chef hanya boleh mengubah ke Diproses atau Siap.']
        );

        if (! $pesanan->bolehKe($data['status'])) {
            return back()->with('error', 'Status tidak bisa diubah dari ' . $pesanan->label_status
                . ' ke ' . Pesanan::STATUS[$data['status']] . '.');
        }

        $pesanan->update(['status' => $data['status']]);

        return back()->with('success', 'Pesanan ' . $pesanan->kode . ' ditandai ' . $pesanan->label_status . '.');
    }

    // Pesanan yang sudah selesai ditangani. Catatan: karena belum ada kolom "siapa yang
    // memproses", ini riwayat dapur secara umum, bukan riwayat per Chef perorangan.
    public function riwayat()
    {
        $pesanans = Pesanan::with('items')
            ->where('status', 'selesai')
            ->latest()
            ->latest('id')
            ->paginate(15);

        return view('chef.riwayat', compact('pesanans'));
    }
}