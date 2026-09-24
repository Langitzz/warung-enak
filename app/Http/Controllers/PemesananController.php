<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PemesananController extends Controller
{
    // Halaman daftar semua menu yang bisa dipesan (bisa difilter lewat ?kategori=id)
    public function menu(Request $request)
    {
        $menus = Menu::with('kategori')
            ->where('tersedia', true)
            ->whereHas('kategori', fn ($q) => $q->where('aktif', true))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori_id', $request->integer('kategori')))
            ->orderBy('nama')
            ->get();

        $kategoriAktif = Kategori::where('aktif', true)->orderBy('nama')->get();
        $kategoriDipilih = $request->filled('kategori')
            ? $kategoriAktif->firstWhere('id', $request->integer('kategori'))
            : null;

        return view('pesan.menu', [
            'menus' => $menus,
            'kategoriAktif' => $kategoriAktif,
            'kategoriDipilih' => $kategoriDipilih,
        ]);
    }

    // Tambah 1 menu ke keranjang (disimpan di session, bukan database)
    public function tambahKeranjang(Menu $menu)
    {
        if (! $menu->tersedia || ! optional($menu->kategori)->aktif) {
            return back()->with('error', 'Menu ini sedang tidak tersedia.');
        }

        $keranjang = session('keranjang', []);
        $keranjang[$menu->id] = ($keranjang[$menu->id] ?? 0) + 1;
        session(['keranjang' => $keranjang]);

        return back()->with('success', $menu->nama.' ditambahkan ke keranjang.');
    }

    // Halaman isi keranjang
    public function keranjang()
    {
        [$items, $total] = $this->rincianKeranjang(session('keranjang', []));

        return view('pesan.keranjang', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    // Ambil detail + subtotal dari isi keranjang session, sambil nyaring menu
    // yang udah nggak tersedia/dihapus admin selagi ada di keranjang tamu
    private function rincianKeranjang(array $keranjang): array
    {
        $items = collect();
        $total = 0;

        if (! empty($keranjang)) {
            $menus = Menu::with('kategori')->whereIn('id', array_keys($keranjang))->get()->keyBy('id');

            foreach ($keranjang as $menuId => $jumlah) {
                $menu = $menus->get($menuId);

                if (! $menu || ! $menu->tersedia || ! optional($menu->kategori)->aktif) {
                    continue;
                }

                $subtotal = $menu->harga * $jumlah;
                $total += $subtotal;

                $items->push([
                    'menu' => $menu,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ]);
            }
        }

        return [$items, $total];
    }

    // Halaman checkout: isi data pemesan + ringkasan sebelum submit
    public function checkout()
    {
        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index');
        }

        $pengaturan = Pengaturan::ambil();

        if (! $pengaturan->terima_pesanan) {
            return view('pesan.tutup', [
                'pengaturan' => $pengaturan,
            ]);
        }

        [$items, $subtotal] = $this->rincianKeranjang($keranjang);
        $pajak = (int) round($subtotal * $pengaturan->pajak / 100);
        $biayaLayanan = (int) round($subtotal * $pengaturan->biaya_layanan / 100);

        return view('pesan.checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'biayaLayanan' => $biayaLayanan,
            'total' => $subtotal + $pajak + $biayaLayanan,
        ]);
    }

    // Proses checkout jadi pesanan asli lewat Pesanan::buat()
    public function prosesCheckout(Request $request)
    {
        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index');
        }

        if (! Pengaturan::ambil()->terima_pesanan) {
            return redirect()->route('checkout.index');
        }

        $data = $request->validate(
            [
                'nama_pelanggan' => ['required', 'string', 'max:100'],
                'no_whatsapp' => ['required', 'string', 'max:20'],
                'catatan' => ['nullable', 'string', 'max:255'],
            ],
            [
                'nama_pelanggan.required' => 'Nama wajib diisi.',
                'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            ]
        );

        $items = collect($keranjang)
            ->map(fn ($jumlah, $menuId) => ['menu_id' => (int) $menuId, 'jumlah' => (int) $jumlah])
            ->values()
            ->all();

        try {
            $pesanan = Pesanan::buat($data, $items, auth()->id());
        } catch (ValidationException $e) {
            return redirect()->route('keranjang.index')
                ->with('error', collect($e->errors())->flatten()->first());
        }

        session()->forget('keranjang');
        session(['pesanan_terakhir_id' => $pesanan->id]);

        return redirect()->route('pesanan.konfirmasi', $pesanan->kode);
    }

    // Halaman konfirmasi. Cuma bisa dibuka oleh yang berhak (lihat bolehAksesPesanan()).
    // "Baru checkout" dicek terpisah, khusus buat nentuin judul halaman
    // ("Berhasil Dibuat" vs "Detail Pesanan") — bukan syarat akses.
    public function konfirmasi(string $kode)
    {
        $pesanan = $this->cariPesananLewatKode($kode);

        abort_if(! $pesanan, 404);
        abort_unless($this->bolehAksesPesanan($pesanan), 404);

        return view('pesan.konfirmasi', [
            'pesanan' => $pesanan,
            'baruCheckout' => session('pesanan_terakhir_id') === $pesanan->id,
        ]);
    }

    // Batalkan pesanan sendiri (cuma boleh selagi masih menunggu/diproses)
    public function batalkan(string $kode)
    {
        $pesanan = $this->cariPesananLewatKode($kode);

        abort_if(! $pesanan, 404);
        abort_unless($this->bolehAksesPesanan($pesanan), 404);

        if (! $pesanan->bolehKe('dibatalkan')) {
            return back()->with('error', 'Pesanan ini sudah tidak bisa dibatalkan.');
        }

        $pesanan->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // Halaman form "Cek Pesanan" buat tamu yang sudah menutup tab konfirmasinya
    public function formCekPesanan()
    {
        return view('pesan.cek');
    }

    // Cocokkan kode pesanan + nomor WhatsApp, kalau cocok buka akses ke halaman konfirmasi
    public function cekPesanan(Request $request)
    {
        $data = $request->validate(
            [
                'kode' => ['required', 'string'],
                'no_whatsapp' => ['required', 'string'],
            ],
            [
                'kode.required' => 'Kode pesanan wajib diisi.',
                'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            ]
        );

        $pesanan = $this->cariPesananLewatKode($data['kode']);
        $cocok = $pesanan && $this->normalisasiWa($data['no_whatsapp']) === $this->normalisasiWa($pesanan->no_whatsapp);

        if (! $cocok) {
            return back()->withInput()->with('error', 'Kode pesanan atau nomor WhatsApp tidak cocok.');
        }

        session(['pesanan_dicek_id' => $pesanan->id]);

        return redirect()->route('pesanan.konfirmasi', $pesanan->kode);
    }

    // Cari pesanan dari kode (mis. "PSN-0008"), aman walau kode salah format
    private function cariPesananLewatKode(string $kode): ?Pesanan
    {
        $id = (int) str_replace('PSN-', '', strtoupper(trim($kode)));
        $pesanan = Pesanan::with('items')->find($id);

        return ($pesanan && $pesanan->kode === strtoupper(trim($kode))) ? $pesanan : null;
    }

    // Boleh akses pesanan ini kalau: baru aja checkout, baru aja lolos Cek
    // Pesanan (dua-duanya lewat session), atau pemilik akunnya sendiri
    private function bolehAksesPesanan(Pesanan $pesanan): bool
    {
        return session('pesanan_terakhir_id') === $pesanan->id
            || session('pesanan_dicek_id') === $pesanan->id
            || (auth()->check() && $pesanan->user_id === auth()->id());
    }

    // Nomor WA dipakai buat dibandingkan, bukan disimpan — 08xxx dan 62xxx dianggap sama
    private function normalisasiWa(?string $nomor): string
    {
        $angka = preg_replace('/\D+/', '', $nomor ?? '');

        if (str_starts_with($angka, '62')) {
            $angka = '0'.substr($angka, 2);
        }

        return $angka;
    }

    // Riwayat pesanan milik akun yang lagi login
    public function riwayatSaya()
    {
        $pesanans = Pesanan::withSum('items as jumlah_porsi', 'jumlah')
            ->where('user_id', auth()->id())
            ->latest()
            ->latest('id')
            ->paginate(10);

        return view('pesan.riwayat', [
            'pesanans' => $pesanans,
        ]);
    }

    // Ubah jumlah satu item di keranjang
    public function ubahKeranjang(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $keranjang = session('keranjang', []);

        if (isset($keranjang[$menu->id])) {
            $keranjang[$menu->id] = $data['jumlah'];
            session(['keranjang' => $keranjang]);
        }

        return back();
    }

    // Hapus satu item dari keranjang
    public function hapusKeranjang(Menu $menu)
    {
        $keranjang = session('keranjang', []);
        unset($keranjang[$menu->id]);
        session(['keranjang' => $keranjang]);

        return back()->with('success', 'Menu dihapus dari keranjang.');
    }
}
