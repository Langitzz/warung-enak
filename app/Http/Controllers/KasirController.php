<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    // Halaman kasir (POS): grid produk yang bisa diklik + keranjang + keypad bayar
    public function index()
    {
        $menus = Menu::with('kategori')
            ->where('tersedia', true)
            ->whereHas('kategori', fn ($q) => $q->where('aktif', true))
            ->orderBy('nama')
            ->get();

        return view('kasir.index', [
            'menus' => $menus,
        ]);
    }

    // Proses bayar dari keranjang kasir (dipanggil lewat fetch/AJAX dari halaman kasir).
    // Status langsung "selesai" karena pembayaran diterima tunai saat itu juga di kasir,
    // beda dengan pesanan lewat landing page yang mulai dari "menunggu".
    public function bayar(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $pesanan = Pesanan::buat(
                [
                    'nama_pelanggan' => 'Pelanggan Kasir',
                    'no_whatsapp' => '-',
                    'catatan' => 'Dibuat lewat halaman Kasir',
                ],
                $data['items'],
                null,
                'selesai'
            );
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }

        $pesanan->load('items');

        return response()->json([
            'kode' => $pesanan->kode,
            'waktu' => $pesanan->created_at->locale('id')->translatedFormat('d M Y, H:i'),
            'subtotal' => $pesanan->subtotal,
            'pajak' => $pesanan->pajak,
            'biaya_layanan' => $pesanan->biaya_layanan,
            'total' => $pesanan->total,
            'items' => $pesanan->items->map(fn ($item) => [
                'nama' => $item->nama_menu,
                'jumlah' => $item->jumlah,
                'harga' => $item->harga,
                'subtotal' => $item->subtotal,
            ]),
        ]);
    }
}