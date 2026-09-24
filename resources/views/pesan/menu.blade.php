@extends('layouts.user')

@section('title', 'Menu')

@section('content')

    <section class="section pricing" style="padding-top: 120px;">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $kategoriDipilih ? $kategoriDipilih->nama : 'Semua Menu' }}</h2>
            <p>Pilih menu yang mau kamu pesan</p>
        </div>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('menu.index') }}"
                        class="btn btn-sm {{ ! $kategoriDipilih ? 'btn-dark' : 'btn-outline-dark' }} rounded-pill">
                        Semua
                    </a>
                    @foreach ($kategoriAktif as $kategori)
                        <a href="{{ route('menu.index', ['kategori' => $kategori->id]) }}"
                            class="btn btn-sm {{ $kategoriDipilih && $kategoriDipilih->id === $kategori->id ? 'btn-dark' : 'btn-outline-dark' }} rounded-pill">
                            {{ $kategori->nama }}
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('keranjang.index') }}" class="btn-get-started">
                    <i class="bi bi-cart3"></i> Lihat Keranjang
                </a>
            </div>

            @if ($menus->isEmpty())
                <p class="text-center text-muted">Belum ada menu yang tersedia saat ini.</p>
            @else
                <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
                    @foreach ($menus as $menu)
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
                                    <button type="submit" class="buy-btn border-0">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection
