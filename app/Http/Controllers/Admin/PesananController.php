<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pengeluaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    // Halaman daftar pesanan
    public function index()
    {
        // jumlah_porsi = total semua porsi di tiap pesanan (dihitung database, satu query)
        $pesanans = Pesanan::withSum('items as jumlah_porsi', 'jumlah')
            ->latest()
            ->latest('id')
            ->get();

        // Jumlah pesanan per status untuk kartu ringkasan di atas tabel
        $hitungStatus = Pesanan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.pesanan.index', [
            'pesanans'     => $pesanans,
            'hitungStatus' => $hitungStatus,
            'statusList'   => Pesanan::STATUS,
        ]);
    }

    // Halaman form tambah pesanan manual
    public function create()
    {
        // Hanya menu yang tersedia dan kategorinya aktif yang bisa dipilih
        $menus = Menu::where('tersedia', true)
            ->whereHas('kategori', fn ($q) => $q->where('aktif', true))
            ->with('kategori')
            ->orderBy('nama')
            ->get();

        return view('admin.pesanan.create', compact('menus'));
    }

    // Simpan pesanan baru
    public function store(Request $request)
    {
        // Yang dikirim dari form hanya menu_id dan jumlah. Harga TIDAK diambil dari form:
        // Pesanan::buat() membacanya dari database.
        $data = $request->validate(
            [
                'nama_pelanggan'  => ['required', 'string', 'max:100'],
                'no_whatsapp'     => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
                'catatan'         => ['nullable', 'string', 'max:255'],
                'items'           => ['required', 'array', 'min:1'],
                'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
                'items.*.jumlah'  => ['required', 'integer', 'min:1', 'max:99'],
            ],
            [
                'nama_pelanggan.required'  => 'Nama pelanggan wajib diisi.',
                'nama_pelanggan.max'       => 'Nama pelanggan maksimal 100 karakter.',
                'no_whatsapp.required'     => 'Nomor WhatsApp wajib diisi.',
                'no_whatsapp.regex'        => 'Nomor hanya boleh berisi angka, spasi, dan tanda + - ( ).',
                'no_whatsapp.max'          => 'Nomor maksimal 20 karakter.',
                'catatan.max'              => 'Catatan maksimal 255 karakter.',
                'items.required'           => 'Pilih minimal satu menu.',
                'items.min'                => 'Pilih minimal satu menu.',
                'items.*.menu_id.required' => 'Menu wajib dipilih di setiap baris.',
                'items.*.menu_id.exists'   => 'Menu yang dipilih tidak valid.',
                'items.*.jumlah.required'  => 'Jumlah wajib diisi di setiap baris.',
                'items.*.jumlah.min'       => 'Jumlah minimal 1.',
                'items.*.jumlah.max'       => 'Jumlah maksimal 99 per menu.',
            ]
        );

        // Pesanan manual dari admin: pemesannya dianggap tamu (tanpa akun).
        // Kalau ada menu yang tidak tersedia, buat() melempar pesan error yang otomatis
        // kembali ke form dengan isian yang tadi.
        $pesanan = Pesanan::buat(
            [
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_whatsapp'    => $data['no_whatsapp'],
                'catatan'        => $data['catatan'] ?? null,
            ],
            $data['items']
        );

        return redirect()
            ->route('admin.pesanan.show', $pesanan)
            ->with('success', 'Pesanan ' . $pesanan->kode . ' berhasil dibuat.');
    }

    // Halaman detail pesanan
    public function show(Pesanan $pesanan)
    {
        $pesanan->load('items', 'user');

        return view('admin.pesanan.show', compact('pesanan'));
    }

    // Ubah status pesanan (menunggu, diproses, selesai, dibatalkan)
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate(
            ['status' => ['required', 'in:' . implode(',', array_keys(Pesanan::STATUS))]],
            ['status.required' => 'Status wajib dipilih.', 'status.in' => 'Status tidak valid.']
        );

        // Hanya perpindahan yang sah yang diterima (lihat ALUR di model Pesanan)
        if (! $pesanan->bolehKe($data['status'])) {
            return redirect()
                ->route('admin.pesanan.show', $pesanan)
                ->with('error', 'Status tidak bisa diubah dari ' . $pesanan->label_status
                    . ' ke ' . Pesanan::STATUS[$data['status']] . '.');
        }

        $pesanan->update(['status' => $data['status']]);

        return redirect()
            ->route('admin.pesanan.show', $pesanan)
            ->with('success', 'Status pesanan berhasil diubah menjadi ' . $pesanan->label_status . '.');
    }

    // Hapus pesanan (hanya yang dibatalkan)
    public function destroy(Pesanan $pesanan)
    {
        if (! $pesanan->bolehDihapus()) {
            return redirect()
                ->route('admin.pesanan.index')
                ->with('error', 'Hanya pesanan yang dibatalkan yang bisa dihapus.');
        }

        $kode = $pesanan->kode;

        // Isi pesanan ikut terhapus otomatis (cascadeOnDelete di database)
        $pesanan->delete();

        return redirect()
            ->route('admin.pesanan.index')
            ->with('success', 'Pesanan ' . $kode . ' berhasil dihapus.');
    }

    // Halaman Riwayat Pesanan (pesanan yang sudah selesai atau dibatalkan)
    public function riwayat()
    {
        $riwayats = Pesanan::withSum('items as jumlah_porsi', 'jumlah')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->latest('id')
            ->paginate(15);

        $jumlahSelesai = Pesanan::where('status', 'selesai')->count();
        $jumlahDibatalkan = Pesanan::where('status', 'dibatalkan')->count();

        return view('admin.laporan.riwayat', [
            'riwayats' => $riwayats,
            'jumlahSelesai' => $jumlahSelesai,
            'jumlahDibatalkan' => $jumlahDibatalkan,
        ]);
    }

    // Halaman Laporan Penjualan, dengan pilihan rentang: minggu (7 hari),
    // bulan (30 hari, per hari), atau tahun (12 bulan terakhir, per bulan).
    // Hanya menghitung pesanan yang sudah selesai.
    public function laporanPenjualan(Request $request)
    {
        $rentang = $request->get('rentang', 'minggu');
        if (! in_array($rentang, ['minggu', 'bulan', 'tahun'], true)) {
            $rentang = 'minggu';
        }

        if ($rentang === 'tahun') {
            $mulai = now('Asia/Jakarta')->subMonths(11)->startOfMonth();
            $selesai = now('Asia/Jakarta')->endOfMonth();

            $dataPerPeriode = Pesanan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as periode, COUNT(*) as jumlah_pesanan, SUM(total) as pendapatan")
                ->where('status', 'selesai')
                ->whereBetween('created_at', [$mulai, $selesai])
                ->groupBy('periode')
                ->get()
                ->keyBy('periode');

            $penjualanPerPeriode = [];
            for ($i = 11; $i >= 0; $i--) {
                $bulan = now('Asia/Jakarta')->subMonths($i);
                $baris = $dataPerPeriode->get($bulan->format('Y-m'));

                $penjualanPerPeriode[] = [
                    'label_panjang' => $bulan->locale('id')->translatedFormat('F Y'),
                    'label_singkat' => $bulan->locale('id')->translatedFormat('M Y'),
                    'pesanan' => $baris->jumlah_pesanan ?? 0,
                    'pendapatan' => (int) ($baris->pendapatan ?? 0),
                ];
            }
        } else {
            $jumlahHari = $rentang === 'bulan' ? 29 : 6;
            $mulai = now('Asia/Jakarta')->subDays($jumlahHari)->startOfDay();
            $selesai = now('Asia/Jakarta')->endOfDay();

            $dataPerPeriode = Pesanan::selectRaw('DATE(created_at) as periode, COUNT(*) as jumlah_pesanan, SUM(total) as pendapatan')
                ->where('status', 'selesai')
                ->whereBetween('created_at', [$mulai, $selesai])
                ->groupBy('periode')
                ->get()
                ->keyBy('periode');

            $penjualanPerPeriode = [];
            for ($i = $jumlahHari; $i >= 0; $i--) {
                $tanggal = now('Asia/Jakarta')->subDays($i);
                $baris = $dataPerPeriode->get($tanggal->format('Y-m-d'));

                $penjualanPerPeriode[] = [
                    'label_panjang' => $tanggal->locale('id')->translatedFormat('l, d F Y'),
                    'label_singkat' => $rentang === 'bulan'
                        ? $tanggal->locale('id')->translatedFormat('d/m')
                        : $tanggal->locale('id')->translatedFormat('D d/m'),
                    'pesanan' => $baris->jumlah_pesanan ?? 0,
                    'pendapatan' => (int) ($baris->pendapatan ?? 0),
                ];
            }
        }

        $totalPendapatan = collect($penjualanPerPeriode)->sum('pendapatan');
        $totalPesanan = collect($penjualanPerPeriode)->sum('pesanan');
        $rataRata = $totalPesanan > 0 ? round($totalPendapatan / $totalPesanan) : 0;

        $totalPengeluaran = Pengeluaran::whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->sum('jumlah');
        $labaBersih = $totalPendapatan - $totalPengeluaran;

        return view('admin.laporan.penjualan', [
            'rentang' => $rentang,
            'penjualanPerPeriode' => $penjualanPerPeriode,
            'totalPendapatan' => $totalPendapatan,
            'totalPesanan' => $totalPesanan,
            'rataRata' => $rataRata,
            'totalPengeluaran' => $totalPengeluaran,
            'labaBersih' => $labaBersih,
        ]);
    }
}