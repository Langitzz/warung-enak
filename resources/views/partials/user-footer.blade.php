@php
    $namaWarung = \App\Models\Pengaturan::ambil()->nama_warung ?: 'Warung Enak';
@endphp

<footer id="footer" class="footer dark-background">

    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                    <span class="sitename">{{ $namaWarung }}</span>
                </a>
                <p>{{ \App\Models\Pengaturan::ambil()->alamat ?: 'Alamat warung belum diisi.' }}</p>
                <div class="social-links d-flex mt-4">
                    <a href="#"><i class="bi bi-whatsapp"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Tautan</h4>
                <ul>
                    <li><a href="#hero">Beranda</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-3 footer-contact text-center text-md-start">
                <h4>Kontak Kami</h4>
                <p>{{ \App\Models\Pengaturan::ambil()->alamat ?: '-' }}</p>
                <p class="mt-3"><strong>Telepon:</strong>
                    <span>{{ \App\Models\Pengaturan::ambil()->telepon ?: '-' }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p>&copy; {{ now()->year }} <strong class="px-1 sitename">{{ $namaWarung }}</strong> <span>Hak cipta
                dilindungi.</span></p>
    </div>

</footer>