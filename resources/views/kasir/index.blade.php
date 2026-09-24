@extends('layouts.kasir')
@section('title', 'Kasir')

@push('styles')
    <style>
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

            .kartu-pesanan {
                flex: 0 0 auto;
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

        .produk-btn {
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
                    <div class="row g-2 produk-scroll" id="daftarProduk">
                        @forelse ($menus as $menu)
                            <div class="col-6 col-md-4">
                                <button type="button" class="produk-btn w-100 d-flex align-items-center"
                                    data-id="{{ $menu->id }}" data-nama="{{ $menu->nama }}"
                                    data-harga="{{ (int) $menu->harga }}">
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
                </div>
            </div>
        </div>

        {{-- Kolom kanan: menu dipesan, keypad angka, dan metode pembayaran --}}
        <div class="col-lg-5 kolom-kanan">

            <div class="card mb-2 kartu-pesanan">
                <div class="card-body">
                    <h5 class="card-title mb-2">Menu Dipesan</h5>
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
                                    <td colspan="4" class="text-center text-muted">Belum ada menu dipesan</td>
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
                        <button type="button" class="btn btn-primary btn-sm" disabled>Cash</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                            title="Segera hadir">Debit</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                            title="Segera hadir">QRIS</button>
                    </div>

                    <label class="form-label mb-1 small">Uang Dibayar</label>
                    <input type="text" class="form-control text-end mb-2" id="labelBayar" value="Rp0" readonly>

                    <div class="row g-1 keypad-wrap">
                        @foreach (['1', '2', '3', '4', '5', '6', '7', '8', '9'] as $angka)
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-dark w-100 keypad-btn tombol-angka"
                                    data-angka="{{ $angka }}">{{ $angka }}</button>
                            </div>
                        @endforeach
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-secondary w-100 keypad-btn" id="btnHapusAngka">
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
                        <span class="fw-semibold">Kembalian</span>
                        <span class="fw-bold" id="labelKembalian">Rp0</span>
                    </div>

                    <button type="button" class="btn btn-success w-100" id="btnBayar" disabled>
                        <i class="mdi mdi-cash-register"></i> Proses Bayar
                    </button>
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const formatRupiah = (angka) => 'Rp' + Number(angka).toLocaleString('id-ID');

            function hitungTotal() {
                return Object.values(pesanan).reduce((total, item) => total + (item.harga * item.qty), 0);
            }

            function renderPesanan() {
                const tbody = document.getElementById('daftarPesanan');
                const items = Object.entries(pesanan);

                if (items.length === 0) {
                    tbody.innerHTML =
                        '<tr id="pesananKosong"><td colspan="4" class="text-center text-muted">Belum ada menu dipesan</td></tr>';
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

                const total = hitungTotal();
                document.getElementById('labelTotal').textContent = formatRupiah(total);
                hitungKembalian();
            }

            function hitungKembalian() {
                const total = hitungTotal();
                const kembalian = uangBayar - total;
                const label = document.getElementById('labelKembalian');
                label.textContent = formatRupiah(kembalian < 0 ? 0 : kembalian);
                document.getElementById('btnBayar').disabled = total <= 0 || uangBayar < total;
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
                tombol.innerHTML = 'Memproses...';

                fetch('{{ route('kasir.bayar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            items
                        }),
                    })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal memproses pembayaran.');
                        return data;
                    })
                    .then((data) => {
                        alert('Pembayaran berhasil! Kode pesanan: ' + data.kode);
                        Object.keys(pesanan).forEach((id) => delete pesanan[id]);
                        uangBayar = 0;
                        document.getElementById('labelBayar').value = formatRupiah(0);
                        renderPesanan();
                    })
                    .catch((err) => {
                        alert(err.message);
                    })
                    .finally(() => {
                        tombol.innerHTML = teksAsli;
                        hitungKembalian();
                    });
            });

            renderPesanan();
        })();
    </script>
@endpush
