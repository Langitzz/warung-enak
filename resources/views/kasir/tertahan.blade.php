@extends('layouts.kasir')
@section('title', 'Pesanan Tertahan')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Pesanan Tertahan</h4>
        <a href="{{ route('kasir.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="mdi mdi-arrow-left"></i> Kembali ke Kasir
        </a>
    </div>

    @php
        $warnaStatus = [
            'menunggu' => 'badge-warning',
            'diproses' => 'badge-info',
            'siap' => 'badge-primary',
            'selesai' => 'badge-success',
            'dibatalkan' => 'badge-danger',
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan / Meja</th>
                            <th>Status</th>
                            <th class="text-center">Porsi</th>
                            <th class="text-end">Total</th>
                            <th>Waktu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanans as $pesanan)
                            <tr>
                                <td class="fw-semibold">{{ $pesanan->kode }}</td>
                                <td>{{ $pesanan->nama_pelanggan }}</td>
                                <td><span class="badge {{ $warnaStatus[$pesanan->status] }}">{{ $pesanan->label_status }}</span></td>
                                <td class="text-center">{{ $pesanan->jumlah_porsi }}</td>
                                <td class="text-end">Rp{{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                <td>{{ $pesanan->created_at->locale('id')->translatedFormat('d M, H:i') }}</td>
                                <td class="text-center">
                                    @if ($pesanan->status === 'siap')
                                        <button type="button" class="btn btn-success btn-sm tombol-bayar"
                                            data-id="{{ $pesanan->id }}"
                                            data-kode="{{ $pesanan->kode }}"
                                            data-total="{{ $pesanan->total }}">
                                            <i class="mdi mdi-cash-register"></i> Bayar
                                        </button>
                                    @else
                                        <span class="text-muted small">Menunggu dapur</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan yang ditahan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal isi uang dibayar --}}
    <div class="modal fade" id="modalBayarTertahan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bayar Pesanan <span id="bayarKode"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Tagihan</span>
                        <span class="fw-bold fs-5" id="bayarTotal"></span>
                    </div>

                    <label for="inputUangBayar" class="form-label">Uang Dibayar</label>
                    <input type="number" class="form-control mb-2" id="inputUangBayar" placeholder="0" min="0">

                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill" id="btnBayarUangPas">Uang Pas</button>
                        @foreach ([20000, 50000, 100000] as $nominal)
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill tombol-nominal-tertahan"
                                data-nominal="{{ $nominal }}">{{ number_format($nominal / 1000, 0, ',', '.') }}rb</button>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Kembalian</span>
                        <span class="fw-bold" id="bayarKembalian">Rp0</span>
                    </div>

                    <div class="text-danger small mt-2 d-none" id="bayarError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnKonfirmasiBayar">
                        <i class="mdi mdi-cash-register"></i> Selesaikan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal struk, gayanya sama dengan halaman Kasir utama --}}
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
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span id="strukSubtotal"></span></div>
                    <div class="d-flex justify-content-between"><span>Pajak</span><span id="strukPajak"></span></div>
                    <div class="d-flex justify-content-between"><span>Biaya Layanan</span><span id="strukBiayaLayanan"></span></div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-1 mt-1"><span>Total</span><span id="strukTotal"></span></div>
                    <div class="d-flex justify-content-between mt-2"><span>Bayar</span><span id="strukBayar"></span></div>
                    <div class="d-flex justify-content-between"><span>Kembalian</span><span id="strukKembalian"></span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnCetakStruk">
                        <i class="mdi mdi-printer"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">Selesai</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
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
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const modalBayar = new bootstrap.Modal(document.getElementById('modalBayarTertahan'));
            const modalStruk = new bootstrap.Modal(document.getElementById('modalStruk'));
            const urlSelesaikanTemplate = '{{ route('kasir.selesaikan', ['pesanan' => 'GANTI_ID']) }}';

            const formatRupiah = (angka) => 'Rp' + Number(angka).toLocaleString('id-ID');

            let pesananIdAktif = null;
            let totalAktif = 0;

            function hitungKembalianTertahan() {
                const bayar = Number(document.getElementById('inputUangBayar').value || 0);
                const kembalian = bayar - totalAktif;
                document.getElementById('bayarKembalian').textContent = formatRupiah(kembalian < 0 ? 0 : kembalian);
            }

            document.querySelectorAll('.tombol-bayar').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    pesananIdAktif = btn.dataset.id;
                    totalAktif = Number(btn.dataset.total);

                    document.getElementById('bayarKode').textContent = btn.dataset.kode;
                    document.getElementById('bayarTotal').textContent = formatRupiah(totalAktif);
                    document.getElementById('inputUangBayar').value = '';
                    document.getElementById('bayarKembalian').textContent = 'Rp0';
                    document.getElementById('bayarError').classList.add('d-none');

                    modalBayar.show();
                });
            });

            document.getElementById('inputUangBayar').addEventListener('input', hitungKembalianTertahan);

            document.getElementById('btnBayarUangPas').addEventListener('click', function () {
                document.getElementById('inputUangBayar').value = totalAktif;
                hitungKembalianTertahan();
            });

            document.querySelectorAll('.tombol-nominal-tertahan').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const sekarang = Number(document.getElementById('inputUangBayar').value || 0);
                    document.getElementById('inputUangBayar').value = sekarang + Number(btn.dataset.nominal);
                    hitungKembalianTertahan();
                });
            });

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
                document.getElementById('strukBayar').textContent = formatRupiah(uangDibayar);
                document.getElementById('strukKembalian').textContent = formatRupiah(uangDibayar - data.total);

                modalStruk.show();
            }

            document.getElementById('btnCetakStruk').addEventListener('click', function () {
                window.print();
            });

            document.getElementById('btnKonfirmasiBayar').addEventListener('click', function () {
                const uangBayar = Number(document.getElementById('inputUangBayar').value || 0);
                const errorEl = document.getElementById('bayarError');

                if (uangBayar < totalAktif) {
                    errorEl.textContent = 'Uang dibayar kurang dari total tagihan.';
                    errorEl.classList.remove('d-none');
                    return;
                }

                const tombol = this;
                tombol.disabled = true;

                fetch(urlSelesaikanTemplate.replace('GANTI_ID', pesananIdAktif), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ uang_dibayar: uangBayar }),
                    })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menyelesaikan pembayaran.');
                        return data;
                    })
                    .then((data) => {
                        modalBayar.hide();
                        tampilkanStruk(data, uangBayar);
                    })
                    .catch((err) => {
                        errorEl.textContent = err.message;
                        errorEl.classList.remove('d-none');
                    })
                    .finally(() => {
                        tombol.disabled = false;
                    });
            });
        })();
    </script>
@endpush