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
                'selesai',
                'kasir'
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

    // Tahan pesanan tanpa langsung dibayar (dine-in yang dibayar belakangan).
    // Statusnya "menunggu", mengikuti alur dapur seperti biasa (menunggu -> diproses -> siap).
    // Pembayarannya diselesaikan nanti lewat halaman Pesanan Tertahan, begitu statusnya "siap".
    public function tahan(Request $request)
    {
        $data = $request->validate(
            [
                'nama_pelanggan' => ['required', 'string', 'max:100'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.menu_id' => ['required', 'integer'],
                'items.*.jumlah' => ['required', 'integer', 'min:1'],
            ],
            [
                'nama_pelanggan.required' => 'Isi nama pelanggan atau nomor meja dulu.',
            ]
        );

        try {
            $pesanan = Pesanan::buat(
                [
                    'nama_pelanggan' => $data['nama_pelanggan'],
                    'no_whatsapp' => '-',
                    'catatan' => 'Ditahan lewat halaman Kasir',
                ],
                $data['items'],
                null,
                'menunggu',
                'kasir'
            );
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }

        return response()->json([
            'kode' => $pesanan->kode,
        ]);
    }

    // Daftar pesanan yang ditahan lewat kasir dan belum lunas
    public function tertahan()
    {
        $pesanans = Pesanan::where('sumber', 'kasir')
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->withSum('items as jumlah_porsi', 'jumlah')
            ->latest('id')
            ->get();

        return view('kasir.tertahan', compact('pesanans'));
    }

    // Selesaikan pembayaran pesanan yang ditahan (hanya boleh kalau statusnya sudah "siap")
    public function selesaikan(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate([
            'uang_dibayar' => ['required', 'integer', 'min:0'],
        ]);

        if (! $pesanan->bolehKe('selesai')) {
            return response()->json([
                'message' => 'Pesanan ini belum bisa diselesaikan (status masih ' . $pesanan->label_status . ').',
            ], 422);
        }

        if ($data['uang_dibayar'] < $pesanan->total) {
            return response()->json([
                'message' => 'Uang dibayar kurang dari total tagihan.',
            ], 422);
        }

        $pesanan->update(['status' => 'selesai']);
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