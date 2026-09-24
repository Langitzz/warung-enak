<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\PesananItem;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = now('Asia/Jakarta')->startOfDay();
        $kemarin = now('Asia/Jakarta')->subDay()->startOfDay();

        // --- Menu ---
        $totalMenu = Menu::count();
        $menuTersedia = Menu::where('tersedia', true)->count();

        // --- Pesanan hari ini vs kemarin (semua status, soal volume) ---
        $pesananHariIni = Pesanan::whereDate('created_at', $hariIni)->count();
        $pesananKemarin = Pesanan::whereDate('created_at', $kemarin)->count();
        $selisihPesanan = $pesananHariIni - $pesananKemarin;

        if ($selisihPesanan > 0) {
            $notePesanan = "+{$selisihPesanan} dari kemarin";
            $warnaPesanan = 'text-success';
        } elseif ($selisihPesanan < 0) {
            $notePesanan = "{$selisihPesanan} dari kemarin";
            $warnaPesanan = 'text-danger';
        } else {
            $notePesanan = 'Sama seperti kemarin';
            $warnaPesanan = 'text-muted';
        }

        // --- Pendapatan hari ini vs kemarin (hanya pesanan selesai) ---
        $pendapatanHariIni = (int) Pesanan::whereDate('created_at', $hariIni)
            ->where('status', 'selesai')
            ->sum('total');

        $pendapatanKemarin = (int) Pesanan::whereDate('created_at', $kemarin)
            ->where('status', 'selesai')
            ->sum('total');

        if ($pendapatanKemarin > 0) {
            $persenPendapatan = round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100);

            if ($persenPendapatan > 0) {
                $notePendapatan = "+{$persenPendapatan}% dari kemarin";
                $warnaPendapatan = 'text-success';
            } elseif ($persenPendapatan < 0) {
                $notePendapatan = "{$persenPendapatan}% dari kemarin";
                $warnaPendapatan = 'text-danger';
            } else {
                $notePendapatan = 'Sama seperti kemarin';
                $warnaPendapatan = 'text-muted';
            }
        } elseif ($pendapatanHariIni > 0) {
            $notePendapatan = 'Kemarin belum ada pendapatan';
            $warnaPendapatan = 'text-success';
        } else {
            $notePendapatan = 'Belum ada pendapatan';
            $warnaPendapatan = 'text-muted';
        }

        // --- Pesanan selesai & masih diproses, hari ini ---
        $pesananSelesaiHariIni = Pesanan::whereDate('created_at', $hariIni)
            ->where('status', 'selesai')
            ->count();

        $pesananDiprosesHariIni = Pesanan::whereDate('created_at', $hariIni)
            ->whereIn('status', ['menunggu', 'diproses', 'siap'])
            ->count();

        // --- 4 card statistik (dirakit di sini biar Blade tinggal render) ---
        $stats = [
            [
                'label' => 'Total Menu',
                'value' => $totalMenu,
                'icon' => 'mdi-food',
                'color' => 'primary',
                'note' => $menuTersedia.' tersedia',
                'note_color' => 'text-muted',
            ],
            [
                'label' => 'Pesanan Hari Ini',
                'value' => $pesananHariIni,
                'icon' => 'mdi-clipboard-list-outline',
                'color' => 'info',
                'note' => $notePesanan,
                'note_color' => $warnaPesanan,
            ],
            [
                'label' => 'Pendapatan Hari Ini',
                'value' => 'Rp '.number_format($pendapatanHariIni, 0, ',', '.'),
                'icon' => 'mdi-cash-multiple',
                'color' => 'success',
                'note' => $notePendapatan,
                'note_color' => $warnaPendapatan,
            ],
            [
                'label' => 'Pesanan Selesai',
                'value' => $pesananSelesaiHariIni,
                'icon' => 'mdi-check-circle-outline',
                'color' => 'warning',
                'note' => 'dari '.$pesananHariIni.' pesanan',
                'note_color' => 'text-muted',
            ],
        ];

        // --- Grafik penjualan 7 hari terakhir (hanya pesanan selesai) ---
        $mulaiGrafik = now('Asia/Jakarta')->subDays(6)->startOfDay();
        $selesaiGrafik = now('Asia/Jakarta')->endOfDay();

        $dataPerHari = Pesanan::selectRaw('DATE(created_at) as tanggal, SUM(total) as pendapatan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$mulaiGrafik, $selesaiGrafik])
            ->groupBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $grafikLabels = [];
        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now('Asia/Jakarta')->subDays($i);
            $grafikLabels[] = $tanggal->locale('id')->translatedFormat('D');
            $grafikData[] = (int) ($dataPerHari->get($tanggal->format('Y-m-d'))->pendapatan ?? 0);
        }

        // --- Ringkasan hari ini (kolom kanan) ---
        $ringkasan = [
            ['label' => 'Jumlah pesanan', 'value' => $pesananHariIni],
            ['label' => 'Pesanan selesai', 'value' => $pesananSelesaiHariIni],
            ['label' => 'Masih diproses', 'value' => $pesananDiprosesHariIni],
            ['label' => 'Total pendapatan', 'value' => 'Rp '.number_format($pendapatanHariIni, 0, ',', '.')],
        ];

        // --- Pesanan terbaru (5 pesanan terakhir, apapun statusnya) ---
        $pesananTerbaru = Pesanan::withSum('items as jumlah_porsi', 'jumlah')
            ->latest()
            ->latest('id')
            ->take(5)
            ->get();

        // --- Menu terlaris (30 hari terakhir, hanya dari pesanan selesai) ---
        $menuTerlaris = PesananItem::whereNotNull('menu_id')
            ->whereHas('pesanan', function ($query) {
                $query->where('status', 'selesai')
                    ->where('created_at', '>=', now('Asia/Jakarta')->subDays(30)->startOfDay());
            })
            ->selectRaw('menu_id, SUM(jumlah) as total_terjual')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(4)
            ->with('menu.kategori')
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'grafikLabels' => $grafikLabels,
            'grafikData' => $grafikData,
            'ringkasan' => $ringkasan,
            'pesananTerbaru' => $pesananTerbaru,
            'menuTerlaris' => $menuTerlaris,
        ]);
    }
}
