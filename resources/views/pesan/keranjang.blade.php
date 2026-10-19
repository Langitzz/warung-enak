@extends('layouts.user')

@section('title', 'Keranjang')

@section('content')

    <section class="section" style="padding-top: 120px;">
        <div class="container section-title" data-aos="fade-up">
            <h2>Keranjang</h2>
            <p>Periksa pesananmu sebelum checkout</p>
        </div>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div id="isiKeranjang">
                @if ($items->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted">Keranjang kamu masih kosong.</p>
                        <a href="{{ route('menu.index') }}" class="btn-get-started">Lihat Menu</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="barisKeranjang">
                                @foreach ($items as $item)
                                    <tr id="baris-menu-{{ $item['menu']->id }}">
                                        <td>
                                            <div class="fw-semibold">{{ $item['menu']->nama }}</div>
                                            <div class="small text-muted">Rp
                                                {{ number_format($item['menu']->harga, 0, ',', '.') }}/porsi</div>
                                        </td>
                                        <td style="max-width: 150px;">
                                            <form action="{{ route('keranjang.ubah', $item['menu']) }}" method="POST"
                                                class="d-flex align-items-center justify-content-center gap-1 form-ubah-jumlah"
                                                data-menu-id="{{ $item['menu']->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-kurang-jumlah"
                                                    {{ $item['jumlah'] <= 1 ? 'disabled' : '' }}>&minus;</button>
                                                <input type="number" name="jumlah" value="{{ $item['jumlah'] }}"
                                                    min="1" max="50" readonly
                                                    class="form-control form-control-sm text-center px-1"
                                                    style="width: 48px;">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-tambah-jumlah"
                                                    {{ $item['jumlah'] >= 50 ? 'disabled' : '' }}>+</button>
                                            </form>
                                        </td>
                                        <td class="text-end" id="subtotal-menu-{{ $item['menu']->id }}">Rp
                                            {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('keranjang.hapus', $item['menu']) }}"
                                                method="POST" class="form-hapus-item"
                                                data-menu-id="{{ $item['menu']->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-end" id="totalKeranjang">Rp
                                        {{ number_format($total, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('menu.index') }}" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Tambah menu lagi
                        </a>
                        <a href="{{ route('checkout.index') }}" class="btn-get-started">
                            Checkout <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('partials.toast-keranjang')

@endsection

@push('scripts')
    <script>
        const formatRupiahKeranjang = (angka) => 'Rp ' + Number(angka).toLocaleString('id-ID');

        function perbaruiTotalKeranjang(total) {
            const el = document.getElementById('totalKeranjang');
            if (el) el.textContent = formatRupiahKeranjang(total);
        }

        function perbaruiBadgeKeranjang(jumlah) {
            const badge = document.getElementById('badgeKeranjang');
            if (!badge) return;
            if (jumlah > 0) {
                badge.textContent = jumlah;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        function tampilkanKeranjangKosong() {
            document.getElementById('isiKeranjang').innerHTML = `
                <div class="text-center py-5">
                    <p class="text-muted">Keranjang kamu masih kosong.</p>
                    <a href="{{ route('menu.index') }}" class="btn-get-started">Lihat Menu</a>
                </div>`;
        }

        // Stepper +/- instan (sama polanya kayak keranjang Kasir), bukan lagi tombol "Ubah" terpisah
        function kirimUbahJumlah(form, jumlahBaru) {
            const inputJumlah = form.querySelector('input[name="jumlah"]');
            const btnKurang = form.querySelector('.btn-kurang-jumlah');
            const btnTambah = form.querySelector('.btn-tambah-jumlah');

            btnKurang.disabled = true;
            btnTambah.disabled = true;

            const formData = new FormData(form);
            formData.set('jumlah', jumlahBaru);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Gagal mengubah jumlah.');
                    return data;
                })
                .then((data) => {
                    inputJumlah.value = jumlahBaru;
                    document.getElementById('subtotal-menu-' + form.dataset.menuId).textContent =
                        formatRupiahKeranjang(data.subtotal);
                    perbaruiTotalKeranjang(data.total);
                    perbaruiBadgeKeranjang(data.jumlah_keranjang);
                })
                .catch((err) => {
                    tampilkanToastKeranjang(err.message, 'error');
                })
                .finally(() => {
                    btnKurang.disabled = jumlahBaru <= 1;
                    btnTambah.disabled = jumlahBaru >= 50;
                });
        }

        document.querySelectorAll('.form-ubah-jumlah').forEach(function(form) {
            const inputJumlah = form.querySelector('input[name="jumlah"]');

            form.querySelector('.btn-kurang-jumlah').addEventListener('click', function() {
                const jumlahBaru = Number(inputJumlah.value) - 1;
                if (jumlahBaru < 1) return;
                kirimUbahJumlah(form, jumlahBaru);
            });

            form.querySelector('.btn-tambah-jumlah').addEventListener('click', function() {
                const jumlahBaru = Number(inputJumlah.value) + 1;
                if (jumlahBaru > 50) return;
                kirimUbahJumlah(form, jumlahBaru);
            });
        });

        document.querySelectorAll('.form-hapus-item').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const tombol = form.querySelector('button');
                tombol.disabled = true;

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                    })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menghapus menu.');
                        return data;
                    })
                    .then((data) => {
                        const baris = document.getElementById('baris-menu-' + form.dataset.menuId);
                        baris.style.transition = 'opacity .2s ease';
                        baris.style.opacity = '0';

                        setTimeout(function() {
                            baris.remove();
                            perbaruiBadgeKeranjang(data.jumlah_keranjang);

                            if (data.kosong) {
                                tampilkanKeranjangKosong();
                            } else {
                                perbaruiTotalKeranjang(data.total);
                            }
                        }, 200);

                        tampilkanToastKeranjang('Menu dihapus dari keranjang.', 'success');
                    })
                    .catch((err) => {
                        tampilkanToastKeranjang(err.message, 'error');
                        tombol.disabled = false;
                    });
            });
        });
    </script>
@endpush