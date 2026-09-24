@extends('layouts.user')

@section('title', 'Beranda')

@section('content')

    @if (session('success'))
        <div class="container" style="margin-top: 100px;">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if (session('error'))
        <div class="container" style="margin-top: 100px;">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif

    @php
        $pengaturan = \App\Models\Pengaturan::ambil();
        $namaWarung = $pengaturan->nama_warung ?: 'Warung Enak';

        // Menu Favorit: 6 menu tersedia terbaru (kategorinya juga harus aktif).
        // Belum ada data "menu terlaris" yang cukup, jadi sementara pakai ini dulu.
        $menuFavorit = \App\Models\Menu::with('kategori')
            ->where('tersedia', true)
            ->whereHas('kategori', fn($q) => $q->where('aktif', true))
            ->latest()
            ->take(6)
            ->get();

        // Kategori Menu: kategori aktif, sekalian hitung berapa menu tersedia di tiap kategori
        $kategoriAktif = \App\Models\Kategori::where('aktif', true)
            ->withCount(['menus' => fn($q) => $q->where('tersedia', true)])
            ->orderBy('nama')
            ->get();

        // Icon dipilih berdasarkan nama kategori, karena belum ada kolom icon di database
        $ikonKategori = function ($nama) {
            $nama = strtolower($nama);
            if (str_contains($nama, 'minum')) {
                return 'bi-cup-hot';
            }
            if (str_contains($nama, 'snack') || str_contains($nama, 'camilan')) {
                return 'bi-basket';
            }
            if (str_contains($nama, 'favorit') || str_contains($nama, 'promo')) {
                return 'bi-star';
            }
            return 'bi-egg-fried';
        };

        // Nomor WhatsApp warung, diformat sama seperti tombol "Hubungi" di detail pesanan admin
        $nomorWa = preg_replace('/\D+/', '', $pengaturan->telepon ?? '');
        if (str_starts_with($nomorWa, '0')) {
            $nomorWa = '62' . substr($nomorWa, 1);
        }

        // Data asli buat section Tentang, biar nggak cuma klaim kosong
        $jumlahMenuTersedia = \App\Models\Menu::where('tersedia', true)->count();
        $jumlahKategoriAktif = \App\Models\Kategori::where('aktif', true)->count();
    @endphp
    {{-- ================= Hero ================= --}}
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row gy-4 justify-content-between">
                <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up"
                    data-aos-delay="200">
                    <h1>{{ $namaWarung }}</h1>
                    <p>Temukan berbagai pilihan makanan dan minuman favorit dengan rasa nikmat, harga bersahabat,
                        dan proses pemesanan yang mudah.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('menu.index') }}" class="btn-get-started">
                            <i class="bi bi-basket"></i> Pesan Sekarang
                        </a>
                        <a href="{{ route('menu.index') }}" class="btn btn-outline-light rounded-pill px-4">Lihat Menu</a>
                    </div>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                    <img src="{{ asset('user/assets/img/hero-img.svg') }}" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Tentang ================= --}}
    <section id="about" class="about section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up">
                    <img src="{{ asset('user/assets/img/about.jpg') }}" class="img-fluid" alt="">
                </div>
                <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                    <h3>Tentang {{ $namaWarung }}</h3>
                    <p class="fst-italic">
                        {{ $pengaturan->deskripsi ?: 'Deskripsi warung belum diisi di Pengaturan Sistem.' }}
                    </p>
                    <ul>
                        <li><i class="bi bi-check-circle"></i> <span>Bahan selalu segar setiap hari</span></li>
                        <li><i class="bi bi-check-circle"></i> <span>Dimasak langsung saat dipesan</span></li>
                        <li><i class="bi bi-check-circle"></i> <span>Harga bersahabat untuk sehari-hari</span></li>
                    </ul>
                    <p class="fw-semibold">
                        Saat ini tersedia <span class="text-primary">{{ $jumlahMenuTersedia }} menu</span> pilihan
                        dari <span class="text-primary">{{ $jumlahKategoriAktif }} kategori</span>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Menu Favorit ================= --}}
    <section id="menu" class="pricing section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Menu Favorit</h2>
            <p>Pilihan yang sering jadi favorit pelanggan {{ $namaWarung }}</p>
        </div>
        <div class="container">
            @if ($menuFavorit->isEmpty())
                <p class="text-center text-muted">Menu belum tersedia saat ini.</p>
            @else
                <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
                    @foreach ($menuFavorit as $menu)
                        <div class="col-lg-4 col-md-6">
                            <div class="pricing-item">
                                <h3>{{ $menu->nama }}</h3>
                                @if ($menu->foto)
                                    <img src="{{ asset('storage/' . $menu->foto) }}" class="img-fluid"
                                        alt="{{ $menu->nama }}">
                                @else
                                    <img src="{{ asset('user/assets/img/portfolio/product-1.jpg') }}" class="img-fluid"
                                        alt="{{ $menu->nama }}">
                                @endif
                                <div class="price"><sup>Rp</sup>{{ number_format($menu->harga, 0, ',', '.') }}</div>
                                <ul>
                                    <li>{{ $menu->kategori->nama ?? 'Menu' }}</li>
                                    @if ($menu->deskripsi)
                                        <li>{{ Str::limit($menu->deskripsi, 60) }}</li>
                                    @endif
                                </ul>
                                <form action="{{ route('keranjang.tambah', $menu) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="buy-btn border-0">Pesan</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4" data-aos="fade-up">
                    <a href="{{ route('menu.index') }}" class="btn-get-started">Lihat Semua Menu</a>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= Kategori Menu ================= --}}
    <section id="kategori" class="services section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Kategori Menu</h2>
            <p>Cari lebih gampang berdasarkan jenisnya</p>
        </div>
        <div class="container">
            @if ($kategoriAktif->isEmpty())
                <p class="text-center text-muted">Belum ada kategori aktif.</p>
            @else
                <div class="row gy-4">
                    @foreach ($kategoriAktif as $kategori)
                        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ 100 * ($loop->index + 1) }}">
                            <div class="service-item position-relative">
                                <div class="icon">
                                    <i class="bi {{ $ikonKategori($kategori->nama) }}"></i>
                                </div>
                                <h3><a href="{{ route('menu.index', ['kategori' => $kategori->id]) }}"
                                        class="stretched-link">{{ $kategori->nama }}</a></h3>
                                <p>{{ $kategori->menus_count }} menu tersedia</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ================= Cara Pesan ================= --}}
    <section id="cara-pesan" class="section light-background">
        <div class="container section-title" data-aos="fade-up">
            <h2>Cara Pesan</h2>
            <p>Pesan makanan jadi lebih mudah</p>
        </div>
        <div class="container">
            <div class="row gy-4 text-center">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px; background: var(--accent-color); color: #fff; font-size: 1.5rem; font-weight: 700;">
                        1
                    </div>
                    <h5>Pilih Menu</h5>
                    <p class="text-muted">Lihat menu yang tersedia, tambahkan yang kamu mau ke keranjang.</p>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px; background: var(--accent-color); color: #fff; font-size: 1.5rem; font-weight: 700;">
                        2
                    </div>
                    <h5>Atur Keranjang</h5>
                    <p class="text-muted">Sesuaikan jumlah pesanan sebelum lanjut checkout.</p>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px; background: var(--accent-color); color: #fff; font-size: 1.5rem; font-weight: 700;">
                        3
                    </div>
                    <h5>Checkout</h5>
                    <p class="text-muted">Isi nama & nomor WhatsApp, lalu periksa ringkasan pesananmu.</p>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px; background: var(--accent-color); color: #fff; font-size: 1.5rem; font-weight: 700;">
                        4
                    </div>
                    <h5>Pesanan Diproses</h5>
                    <p class="text-muted">Warung akan memproses pesananmu — kamu bisa memantau statusnya.</p>
                </div>
            </div>
            <div class="text-center mt-4" data-aos="fade-up">
                <a href="{{ route('menu.index') }}" class="btn-get-started">Mulai Pesan</a>
            </div>
        </div>
    </section>

    {{-- ================= Kenapa Pilih Kami ================= --}}
    <section id="stats" class="stats section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item text-center w-100 h-100">
                        <span data-purecounter-start="0" data-purecounter-end="25" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Menu Tersedia</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item text-center w-100 h-100">
                        <span data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Pelanggan Puas</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item text-center w-100 h-100">
                        <span data-purecounter-start="0" data-purecounter-end="3" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Tahun Beroperasi</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item text-center w-100 h-100">
                        <span data-purecounter-start="0" data-purecounter-end="1200" data-purecounter-duration="1"
                            class="purecounter"></span>
                        <p>Pesanan Terkirim</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Kontak & Lokasi ================= --}}
    <section id="contact" class="contact section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Kontak & Lokasi</h2>
            <p>Hubungi kami atau datang langsung ke warung</p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-5">
                    <div class="accent-background rounded-4 p-4 h-100" style="background-color: var(--background-color);">
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                            <i class="bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>Alamat</h3>
                                <p class="mb-1">{{ $pengaturan->alamat ?: 'Alamat belum diisi di Pengaturan Sistem' }}
                                </p>
                                @if ($pengaturan->alamat)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($pengaturan->alamat) }}"
                                        target="_blank" rel="noopener" class="small" style="color: #fff;">
                                        <i class="bi bi-map"></i> Lihat di Google Maps
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-clock flex-shrink-0"></i>
                            <div>
                                <h3>Jam Buka</h3>
                                <p>{{ $pengaturan->jam_buka ?? '-' }} - {{ $pengaturan->jam_tutup ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Telepon</h3>
                                <p class="mb-1">{{ $pengaturan->telepon ?: '-' }}</p>
                                @if ($nomorWa)
                                    <a href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener"
                                        class="small" style="color: #fff;">
                                        <i class="bi bi-whatsapp"></i> Chat via WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <form action="#" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Nama" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="subject" placeholder="Subjek" required>
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control" name="message" rows="6" placeholder="Pesan" required></textarea>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit">Kirim Pesan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section id="cta" class="call-to-action section dark-background">
        <div class="container">
            <img src="{{ asset('user/assets/img/cta-bg.jpg') }}" alt="">
            <div class="content text-center" data-aos="zoom-in" data-aos-delay="100">
                <h3>Sudah Tahu Mau Makan Apa?</h3>
                <p>Yuk, pilih menu favoritmu dan pesan sekarang.</p>
                <a href="{{ route('menu.index') }}" class="btn-get-started">
                    <i class="bi bi-basket"></i> Pesan Sekarang
                </a>
            </div>
        </div>
    </section>

@endsection
