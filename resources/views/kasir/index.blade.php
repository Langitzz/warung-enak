@extends('layouts.kasir')
@section('title', 'Kasir')

@push('styles')
    <style>
        /* Cetak struk: sembunyikan semua elemen lain, tampilkan hanya area struk */
        @media print {
            body * {
                visibility: hidden;
            }

            #areaStruk,
            #areaStruk * {
                visibility: visible;
            }

            #areaStruk {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }

        .keypad-wrap {
            max-width: 260px;
            margin: 0 auto;
        }

        .kolom-produk {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .kolom-produk .card-body {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .produk-scroll {
            overflow-y: auto;
            flex: 1 1 auto;
            align-content: flex-start;
        }

        /* Kolom kanan scroll sendiri (independen dari kolom kiri) kalau kontennya
           lebih tinggi dari layar -- bukan maksa Kartu Pesanan jadi kecil banget */
        .kolom-kanan {
            height: 100%;
            overflow-y: auto;
        }

        .kartu-pesanan .card-body {
            padding: 0.85rem 1rem;
        }

        .kartu-pesanan .table-responsive {
            max-height: 320px;
            overflow-y: auto;
        }

        .kartu-pembayaran .card-body {
            padding: 0.85rem 1rem;
        }

        .kartu-pembayaran .card-title {
            margin-bottom: 0.5rem !important;
            font-size: 1rem;
        }

        @media (max-width: 991.98px) {

            .kolom-produk,
            .kolom-kanan {
                height: auto;
                overflow: visible;
            }

            .produk-scroll {
                overflow-y: visible;
                max-height: none;
            }

            .kartu-pesanan .table-responsive {
                overflow-y: visible;
            }
        }

        .keypad-btn {
            aspect-ratio: 1 / 1;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: transform .1s ease, box-shadow .1s ease;
        }

        .keypad-btn:active,
        .keypad-btn.keypad-pressed {
            transform: scale(0.95);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, .15);
        }

        .keypad-btn.tombol-angka.keypad-pressed,
        .keypad-btn.tombol-angka:active {
            background-color: #6c74d6;
            border-color: #6c74d6;
            color: #fff;
        }

        .tombol-metode {
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }

        .produk-btn {
            position: relative;
            appearance: none;
            -webkit-appearance: none;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            text-align: left;
            white-space: normal;
            padding: 0.75rem !important;
            gap: 0.75rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .produk-btn.diklik {
            animation: mantulProduk .3s ease;
        }

        @keyframes mantulProduk {
            0% {
                transform: scale(1);
            }

            40% {
                transform: scale(0.94);
            }

            100% {
                transform: scale(1);
            }
        }

        .total-berubah {
            display: inline-block;
            animation: kedipTotal .3s ease;
        }

        @keyframes kedipTotal {
            0% {
                transform: scale(1);
            }

            40% {
                transform: scale(1.15);
            }

            100% {
                transform: scale(1);
            }
        }

        .tombol-kategori {
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .tombol-kategori.btn-primary {
            box-shadow: 0 2px 6px rgba(var(--bs-primary-rgb), .4);
            transform: translateY(-1px);
        }

        .badge-qty {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 22px;
            height: 22px;
            border-radius: 11px;
            background: #4d55c4;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
        }

        .produk-btn:hover {
            border-color: #6f7bf7;
            box-shadow: 0 6px 16px rgba(90, 100, 220, 0.15);
            transform: translateY(-3px);
        }

        .produk-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(90, 100, 220, 0.15);
        }

        .produk-foto,
        .produk-foto-kosong {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            flex: 0 0 auto;
        }

        .produk-foto {
            object-fit: cover;
        }

        .produk-keterangan {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .produk-foto-kosong {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f2fb;
            color: #6f7bf7;
            font-size: 1.75rem;
        }

        .produk-nama {
            color: #212529;
            line-height: 1.2;
        }

        .produk-kategori {
            color: #868e96;
            text-transform: uppercase;
            letter-spacing: .03em;
            font-size: .72rem;
        }

        .produk-harga {
            color: #4d55c4;
        }

        #daftarPesanan tr td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="row g-3 h-100">

        {{-- Kolom kiri: daftar menu yang bisa diklik untuk ditambahkan ke pesanan --}}
        <div class="col-lg-7 kolom-produk">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Daftar Menu</h5>

                    @php
                        $daftarKategori = $menus->pluck('kategori.nama')->filter()->unique()->sort()->values();
                    @endphp

                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" id="cariMenu" placeholder="Cari menu..."
                            autocomplete="off">
                    </div>

                    <div class="d-flex flex-wrap gap-1 mb-3" id="filterKategori">
                        <button type="button" class="btn btn-sm btn-primary tombol-kategori"
                            data-kategori="semua">Semua</button>
                        @foreach ($daftarKategori as $kategori)
                            <button type="button" class="btn btn-sm btn-outline-secondary tombol-kategori"
                                data-kategori="{{ $kategori }}">{{ $kategori }}</button>
                        @endforeach
                    </div>

                    <div class="row g-2 produk-scroll" id="daftarProduk">
                        @forelse ($menus as $menu)
                            <div class="col-6 col-md-4">
                                <button type="button" class="produk-btn w-100 d-flex align-items-center"
                                    data-id="{{ $menu->id }}" data-nama="{{ $menu->nama }}"
                                    data-harga="{{ (int) $menu->harga }}" data-kategori="{{ $menu->kategori->nama ?? '' }}">
                                    <span class="badge-qty d-none" data-badge-produk="{{ $menu->id }}">0</span>
                                    @if ($menu->foto)
                                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}"
                                            class="produk-foto">
                                    @else
                                        <span class="produk-foto produk-foto-kosong"><i class="mdi mdi-food"></i></span>
                                    @endif
                                    <span class="produk-keterangan">
                                        <span class="fw-semibold produk-nama">{{ $menu->nama }}</span>
                                        <span class="produk-kategori">{{ $menu->kategori->nama ?? '-' }}</span>
                                        <span
                                            class="fw-bold produk-harga">Rp{{ number_format($menu->harga, 0, ',', '.') }}</span>
                                    </span>
                                </button>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted mb-0">Belum ada menu yang tersedia untuk dijual.</p>
                            </div>
                        @endforelse
                    </div>
                    <p class="text-muted text-center mb-0 mt-3 d-none" id="pesanMenuTidakKetemu">
                        Menu tidak ditemukan.
                    </p>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: menu dipesan, keypad angka, dan metode pembayaran --}}
        <div class="col-lg-5 kolom-kanan">

            <div class="card mb-2 kartu-pesanan">
                <div class="card-body">
                    <h5 class="card-title mb-2 d-flex align-items-center gap-2">
                        Menu Dipesan
                        <span class="badge rounded-pill bg-primary" id="badgeJumlahPesanan">0</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="daftarPesanan">
                                <tr id="pesananKosong">
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="mdi mdi-cart-outline" style="font-size: 2rem; opacity: .4;"></i>
                                        <div class="mt-1">Keranjang masih kosong</div>
                                        <div class="small">Klik menu di sebelah kiri buat mulai</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fs-5">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold text-primary" id="labelTotal">Rp0</span>
                    </div>
                </div>
            </div>

            <div class="card mb-2 kartu-pembayaran">
                <div class="card-body">
                    <h5 class="card-title mb-2">Metode Pembayaran</h5>
                    <div class="btn-group w-100 mb-2" role="group">
                        <button type="button" class="btn btn-primary btn-sm tombol-metode"
                            data-metode="tunai">Tunai</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm tombol-metode" data-metode="qris"
                            @if (!$qris) disabled title="QRIS belum diupload di Pengaturan Sistem" @endif>QRIS</button>
                    </div>

                    <div id="areaTunai">
                        <label class="form-label mb-1 small">Uang Dibayar</label>
                        <input type="text" class="form-control text-end mb-2" id="labelBayar" value="Rp0" readonly>

                        <div class="d-flex flex-wrap gap-1 mb-2" id="nominalCepat">
                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill" id="btnUangPas">Uang
                                Pas</button>
                            @foreach ([20000, 50000, 100000] as $nominal)
                                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill tombol-nominal"
                                    data-nominal="{{ $nominal }}">{{ number_format($nominal / 1000, 0, ',', '.') }}rb</button>
                            @endforeach
                        </div>

                        <div class="row g-1 keypad-wrap">
                            @foreach (['1', '2', '3', '4', '5', '6', '7', '8', '9'] as $angka)
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-dark w-100 keypad-btn tombol-angka"
                                        data-angka="{{ $angka }}">{{ $angka }}</button>
                                </div>
                            @endforeach
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 keypad-btn"
                                    id="btnHapusAngka">
                                    <i class="mdi mdi-backspace-outline"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-dark w-100 keypad-btn tombol-angka"
                                    data-angka="0">0</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 keypad-btn"
                                    id="btnClearAngka">C</button>
                            </div>
                        </div>

                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold" id="labelKembalianTeks">Kembalian</span>
                            <span class="fw-bold" id="labelKembalian">Rp0</span>
                        </div>
                    </div>

                    <div id="areaQris" class="d-none text-center mb-3">
                        @if ($qris)
                            <img src="{{ asset('storage/' . $qris) }}" alt="QRIS" class="img-fluid mb-2"
                                style="max-width: 200px;">
                        @endif
                        <p class="text-muted small mb-0">Minta pelanggan pindai QRIS di atas.</p>
                    </div>

                    <div id="areaDebit" class="d-none text-center mb-3">
                        <i class="mdi mdi-credit-card-outline" style="font-size: 48px; opacity: .5;"></i>
                        <p class="text-muted small mb-0">Proses pembayaran lewat mesin EDC, lalu konfirmasi di
                            bawah.</p>
                    </div>

                    <button type="button" class="btn btn-warning w-100 mb-2" id="btnTahan" disabled>
                        <i class="mdi mdi-clock-outline"></i> Tahan Pesanan
                    </button>

                    <button type="button" class="btn btn-success w-100" id="btnBayar" disabled>
                        <i class="mdi mdi-cash-register"></i> Proses Bayar
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal struk setelah pembayaran berhasil --}}
    <div class="modal fade" id="modalStruk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran Berhasil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="areaStruk">
                    <div class="text-center mb-2">
                        <strong>{{ \App\Models\Pengaturan::namaWarung() }}</strong><br>
                        <small class="text-muted">Struk Pembayaran</small>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span id="strukKode"></span>
                        <span id="strukWaktu"></span>
                    </div>
                    <hr>
                    <table class="table table-sm mb-2">
                        <tbody id="strukItems"></tbody>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span id="strukSubtotal"></span>
                    </div>
                    <div class="d-flex justify-content-between"><span>Pajak</span><span id="strukPajak"></span></div>
                    <div class="d-flex justify-content-between"><span>Biaya Layanan</span><span
                            id="strukBiayaLayanan"></span></div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-1 mt-1"><span>Total</span><span
                            id="strukTotal"></span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Metode</span><span id="strukMetode"></span>
                    </div>
                    <div class="d-flex justify-content-between"><span>Bayar</span><span id="strukBayar"></span></div>
                    <div class="d-flex justify-content-between"><span>Kembalian</span><span id="strukKembalian"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnCetakStruk">
                        <i class="mdi mdi-printer"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast notifikasi kecil, pengganti alert() bawaan browser --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastNotif" class="toast align-items-center text-white border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastPesan"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    {{-- Modal isi nama pelanggan/meja saat menahan pesanan --}}
    <div class="modal fade" id="modalTahan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tahan Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label for="namaTahan" class="form-label">Nama Pelanggan / No. Meja</label>
                    <input type="text" class="form-control" id="namaTahan"
                        placeholder="Contoh: Meja 5, atau nama pelanggan" autocomplete="off">
                    <div class="text-danger small mt-1 d-none" id="errorTahan"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" id="btnSimpanTahan">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const pesanan = {};
            let uangBayar = 0;
            let totalSebelumnya = 0;
            let metodeBayar = 'tunai';
            const labelMetode = {
                tunai: 'Tunai',
                debit: 'Debit',
                qris: 'QRIS'
            };
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const elToast = document.getElementById('toastNotif');
            const toastBootstrap = new bootstrap.Toast(elToast, {
                delay: 4000
            });

            function tampilkanToast(pesan, jenis) {
                elToast.classList.remove('bg-success', 'bg-danger');
                elToast.classList.add(jenis === 'error' ? 'bg-danger' : 'bg-success');
                document.getElementById('toastPesan').textContent = pesan;
                toastBootstrap.show();
            }
            const modalStruk = new bootstrap.Modal(document.getElementById('modalStruk'));
            const modalTahan = new bootstrap.Modal(document.getElementById('modalTahan'));

            function tampilkanStruk(data, uangDibayar) {
                document.getElementById('strukKode').textContent = data.kode;
                document.getElementById('strukWaktu').textContent = data.waktu;

                document.getElementById('strukItems').innerHTML = data.items.map((item) => `
                    <tr>
                        <td>${item.nama}<br><small class="text-muted">${item.jumlah} x ${formatRupiah(item.harga)}</small></td>
                        <td class="text-end">${formatRupiah(item.subtotal)}</td>
                    </tr>
                `).join('');

                document.getElementById('strukSubtotal').textContent = formatRupiah(data.subtotal);
                document.getElementById('strukPajak').textContent = formatRupiah(data.pajak);
                document.getElementById('strukBiayaLayanan').textContent = formatRupiah(data.biaya_layanan);
                document.getElementById('strukTotal').textContent = formatRupiah(data.total);
                document.getElementById('strukMetode').textContent = labelMetode[data.metode_bayar] || data
                    .metode_bayar;
                document.getElementById('strukBayar').textContent = formatRupiah(uangDibayar);
                document.getElementById('strukKembalian').textContent = formatRupiah(uangDibayar - data.total);

                modalStruk.show();
            }

            function pilihMetode(metode) {
                metodeBayar = metode;

                document.querySelectorAll('.tombol-metode').forEach(function(btn) {
                    const aktif = btn.dataset.metode === metode;
                    btn.classList.toggle('btn-primary', aktif);
                    btn.classList.toggle('btn-outline-secondary', !aktif);
                });

                document.getElementById('areaTunai').classList.toggle('d-none', metode !== 'tunai');
                document.getElementById('areaQris').classList.toggle('d-none', metode !== 'qris');
                document.getElementById('areaDebit').classList.toggle('d-none', metode !== 'debit');

                const labelTombolBayar = {
                    tunai: '<i class="mdi mdi-cash-register"></i> Proses Bayar',
                    debit: '<i class="mdi mdi-credit-card-outline"></i> Konfirmasi Sudah Dibayar (Kartu)',
                    qris: '<i class="mdi mdi-qrcode"></i> Konfirmasi Sudah Dibayar (QRIS)',
                };
                document.getElementById('btnBayar').innerHTML = labelTombolBayar[metode];

                if (metode === 'tunai') {
                    uangBayar = 0;
                    document.getElementById('labelBayar').value = formatRupiah(0);
                }

                hitungKembalian();
            }

            document.querySelectorAll('.tombol-metode').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (btn.disabled) return;
                    pilihMetode(btn.dataset.metode);
                });
            });

            document.getElementById('btnCetakStruk').addEventListener('click', function() {
                window.print();
            });

            document.getElementById('btnTahan').addEventListener('click', function() {
                document.getElementById('namaTahan').value = '';
                document.getElementById('errorTahan').classList.add('d-none');
                modalTahan.show();
            });

            // Autofocus kolom nama begitu modal selesai muncul (bukan pas .show() dipanggil,
            // biar nggak ketabrak animasi transisi modal yang belum selesai)
            document.getElementById('modalTahan').addEventListener('shown.bs.modal', function() {
                document.getElementById('namaTahan').focus();
            });

            // Enter di kolom nama langsung Simpan, nggak perlu klik tombol
            document.getElementById('namaTahan').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('btnSimpanTahan').click();
                }
            });

            document.getElementById('btnSimpanTahan').addEventListener('click', function() {
                const namaPelanggan = document.getElementById('namaTahan').value.trim();
                const errorEl = document.getElementById('errorTahan');

                if (!namaPelanggan) {
                    errorEl.textContent = 'Isi nama pelanggan atau nomor meja dulu.';
                    errorEl.classList.remove('d-none');
                    return;
                }

                const items = Object.entries(pesanan).map(([id, item]) => ({
                    menu_id: Number(id),
                    jumlah: item.qty
                }));

                const tombol = this;
                tombol.disabled = true;
                const teksAsli = tombol.innerHTML;
                tombol.innerHTML = 'Menyimpan...';

                fetch('{{ route('kasir.tahan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            nama_pelanggan: namaPelanggan,
                            items
                        }),
                    })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menahan pesanan.');
                        return data;
                    })
                    .then((data) => {
                        modalTahan.hide();
                        tampilkanToast('Pesanan ' + data.kode +
                            ' ditahan. Bisa diselesaikan lewat halaman Pesanan Tertahan.', 'success');
                        Object.keys(pesanan).forEach((id) => delete pesanan[id]);
                        renderPesanan();
                    })
                    .catch((err) => {
                        errorEl.textContent = err.message;
                        errorEl.classList.remove('d-none');
                    })
                    .finally(() => {
                        tombol.disabled = false;
                        tombol.innerHTML = teksAsli;
                    });
            });
            const formatRupiah = (angka) => 'Rp' + Number(angka).toLocaleString('id-ID');

            // Filter kategori + cari menu, langsung di browser tanpa reload halaman
            const inputCari = document.getElementById('cariMenu');
            const tombolKategori = document.querySelectorAll('.tombol-kategori');
            let kategoriAktif = 'semua';

            function terapkanFilter() {
                const kataKunci = inputCari.value.trim().toLowerCase();
                let adaYangCocok = false;
                let adaProdukSamaSekali = false;

                document.querySelectorAll('#daftarProduk > div').forEach(function(kolom) {
                    const tombol = kolom.querySelector('.produk-btn');
                    if (!tombol) return;
                    adaProdukSamaSekali = true;

                    const cocokKategori = kategoriAktif === 'semua' || tombol.dataset.kategori ===
                        kategoriAktif;
                    const cocokKata = tombol.dataset.nama.toLowerCase().includes(kataKunci);
                    const cocok = cocokKategori && cocokKata;
                    kolom.style.display = cocok ? '' : 'none';
                    if (cocok) adaYangCocok = true;
                });

                // Pesan "tidak ditemukan" cuma muncul kalau memang ada menu tapi nggak ada yang cocok filter
                // (bukan kalau menunya emang kosong dari awal — itu sudah ada pesannya sendiri di atas)
                document.getElementById('pesanMenuTidakKetemu')
                    .classList.toggle('d-none', !adaProdukSamaSekali || adaYangCocok);
            }

            inputCari.addEventListener('input', terapkanFilter);

            tombolKategori.forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    kategoriAktif = tombol.dataset.kategori;
                    tombolKategori.forEach(function(t) {
                        t.classList.remove('btn-primary');
                        t.classList.add('btn-outline-secondary');
                    });
                    tombol.classList.remove('btn-outline-secondary');
                    tombol.classList.add('btn-primary');
                    terapkanFilter();
                });
            });

            function hitungTotal() {
                return Object.values(pesanan).reduce((total, item) => total + (item.harga * item.qty), 0);
            }

            function renderPesanan() {
                const tbody = document.getElementById('daftarPesanan');
                const items = Object.entries(pesanan);

                if (items.length === 0) {
                    tbody.innerHTML = `
                        <tr id="pesananKosong">
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="mdi mdi-cart-outline" style="font-size: 2rem; opacity: .4;"></i>
                                <div class="mt-1">Keranjang masih kosong</div>
                                <div class="small">Klik menu di sebelah kiri buat mulai</div>
                            </td>
                        </tr>`;
                } else {
                    tbody.innerHTML = items.map(([id, item]) => `
                    <tr>
                        <td>${item.nama}</td>
                        <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-kurang" data-id="${id}">-</button>
                        <span class="mx-2">${item.qty}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-tambah" data-id="${id}">+</button>
                        </td>
                        <td class="text-end">${formatRupiah(item.harga * item.qty)}</td>
                        <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-item" data-id="${id}">
                            <i class="mdi mdi-close"></i>
                        </button>
                        </td>
                    </tr>
                    `).join('');
                }

                const totalPorsi = items.reduce((jumlah, [, item]) => jumlah + item.qty, 0);
                document.getElementById('badgeJumlahPesanan').textContent = totalPorsi;

                const total = hitungTotal();
                const labelTotal = document.getElementById('labelTotal');
                labelTotal.textContent = formatRupiah(total);

                if (total !== totalSebelumnya) {
                    labelTotal.classList.remove('total-berubah');
                    void labelTotal.offsetWidth; // trik yang sama kayak animasi "mantul" di kartu produk
                    labelTotal.classList.add('total-berubah');
                    totalSebelumnya = total;
                }

                hitungKembalian();
                perbaruiBadgeProduk();
            }

            // Badge kecil angka di pojok kartu produk, biar kasir nggak perlu lirik tabel keranjang
            function perbaruiBadgeProduk() {
                document.querySelectorAll('[data-badge-produk]').forEach(function(badge) {
                    const id = badge.dataset.badgeProduk;
                    const item = pesanan[id];
                    if (item) {
                        badge.textContent = item.qty;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                });
            }

            function hitungKembalian() {
                const total = hitungTotal();

                // Debit/QRIS dianggap selalu dibayar pas (nggak ada uang kembali)
                if (metodeBayar !== 'tunai') {
                    uangBayar = total;
                }

                const kembalian = uangBayar - total;
                const label = document.getElementById('labelKembalian');
                const labelTeks = document.getElementById('labelKembalianTeks');

                if (kembalian < 0) {
                    labelTeks.textContent = 'Kurang';
                    label.textContent = formatRupiah(Math.abs(kembalian));
                    label.classList.add('text-danger');
                } else {
                    labelTeks.textContent = 'Kembalian';
                    label.textContent = formatRupiah(kembalian);
                    label.classList.remove('text-danger');
                }

                document.getElementById('btnBayar').disabled = total <= 0 || uangBayar < total;
                document.getElementById('btnTahan').disabled = total <= 0;
            }

            // Klik kartu menu => tambah ke pesanan
            document.getElementById('daftarProduk').addEventListener('click', function(e) {
                const btn = e.target.closest('.produk-btn');
                if (!btn) return;
                const id = btn.dataset.id;
                if (pesanan[id]) {
                    pesanan[id].qty += 1;
                } else {
                    pesanan[id] = {
                        nama: btn.dataset.nama,
                        harga: Number(btn.dataset.harga),
                        qty: 1
                    };
                }

                btn.classList.remove('diklik');
                void btn.offsetWidth; // trik biar animasinya bisa diulang walau class-nya belum sempat lepas
                btn.classList.add('diklik');

                renderPesanan();
            });

            // Tombol +/- dan hapus per item pesanan
            document.getElementById('daftarPesanan').addEventListener('click', function(e) {
                const tambah = e.target.closest('.btn-tambah');
                const kurang = e.target.closest('.btn-kurang');
                const hapus = e.target.closest('.btn-hapus-item');

                if (tambah) {
                    pesanan[tambah.dataset.id].qty += 1;
                } else if (kurang) {
                    pesanan[kurang.dataset.id].qty -= 1;
                    if (pesanan[kurang.dataset.id].qty <= 0) delete pesanan[kurang.dataset.id];
                } else if (hapus) {
                    delete pesanan[hapus.dataset.id];
                } else {
                    return;
                }
                renderPesanan();
            });

            // Keypad angka untuk input "Uang Dibayar"
            function ketikAngka(digit) {
                const angkaBaru = uangBayar === 0 ? digit : String(uangBayar) + digit;
                uangBayar = Number(angkaBaru);
                document.getElementById('labelBayar').value = formatRupiah(uangBayar);
                hitungKembalian();
            }

            function hapusAngkaTerakhir() {
                const teks = String(uangBayar).slice(0, -1);
                uangBayar = teks === '' ? 0 : Number(teks);
                document.getElementById('labelBayar').value = formatRupiah(uangBayar);
                hitungKembalian();
            }

            function clearAngka() {
                uangBayar = 0;
                document.getElementById('labelBayar').value = formatRupiah(uangBayar);
                hitungKembalian();
            }

            // Tombol nominal cepat: "Uang Pas" (samakan dengan total) dan pecahan umum (ditambahkan, bukan menimpa)
            document.getElementById('btnUangPas').addEventListener('click', function() {
                uangBayar = hitungTotal();
                document.getElementById('labelBayar').value = formatRupiah(uangBayar);
                hitungKembalian();
            });

            document.querySelectorAll('.tombol-nominal').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    uangBayar += Number(btn.dataset.nominal);
                    document.getElementById('labelBayar').value = formatRupiah(uangBayar);
                    hitungKembalian();
                });
            });

            function kedipkanTombol(btn) {
                if (!btn) return;
                btn.classList.add('keypad-pressed');
                setTimeout(function() {
                    btn.classList.remove('keypad-pressed');
                }, 150);
            }

            document.querySelectorAll('.tombol-angka').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    ketikAngka(btn.dataset.angka);
                });
            });
            document.getElementById('btnHapusAngka').addEventListener('click', hapusAngkaTerakhir);
            document.getElementById('btnClearAngka').addEventListener('click', clearAngka);

            document.addEventListener('keydown', function(e) {
                if (e.key >= '0' && e.key <= '9') {
                    ketikAngka(e.key);
                    kedipkanTombol(document.querySelector('.tombol-angka[data-angka="' + e.key + '"]'));
                } else if (e.key === 'Backspace') {
                    hapusAngkaTerakhir();
                    kedipkanTombol(document.getElementById('btnHapusAngka'));
                } else if (e.key === 'Delete' || e.key === 'Escape') {
                    clearAngka();
                    kedipkanTombol(document.getElementById('btnClearAngka'));
                }
            });

            // Proses Bayar => kirim ke server (bukan simulasi lagi), pesanan beneran
            // kesimpen ke database lewat Pesanan::buat() dengan status langsung "selesai"
            document.getElementById('btnBayar').addEventListener('click', function() {
                const tombol = this;
                const items = Object.entries(pesanan).map(([id, item]) => ({
                    menu_id: Number(id),
                    jumlah: item.qty
                }));

                if (items.length === 0) return;

                tombol.disabled = true;
                const teksAsli = tombol.innerHTML;
                tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

                fetch('{{ route('kasir.bayar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            items,
                            metode_bayar: metodeBayar,
                        }),
                    })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal memproses pembayaran.');
                        return data;
                    })
                    .then((data) => {
                        tampilkanStruk(data, uangBayar);
                        Object.keys(pesanan).forEach((id) => delete pesanan[id]);
                        uangBayar = 0;
                        document.getElementById('labelBayar').value = formatRupiah(0);
                        pilihMetode('tunai');
                        renderPesanan();
                    })
                    .catch((err) => {
                        tampilkanToast(err.message, 'error');
                    })
                    .finally(() => {
                        tombol.innerHTML = teksAsli;
                        hitungKembalian();
                    });
            });

            // Shortcut: tekan Enter buat langsung Proses Bayar, kecuali lagi ngetik di kotak cari/nama
            // atau ada modal yang lagi kebuka (biar nggak nabrak input di modal Tahan Pesanan)
            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;

                const sedangMengetik = ['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName);
                const adaModalTerbuka = document.querySelector('.modal.show');
                if (sedangMengetik || adaModalTerbuka) return;

                const btn = document.getElementById('btnBayar');
                if (!btn.disabled) btn.click();
            });

            renderPesanan();
        })();
    </script>
@endpush
