@extends('layouts.admin')

@section('title', 'Tambah Pesanan')

@section('content')

    @php
        // Kelompokkan menu per kategori untuk dropdown
        $menuPerKategori = $menus->groupBy(fn($menu) => $menu->kategori->nama);

        // Baris awal: isian sebelumnya kalau validasi gagal, atau satu baris kosong
        $barisAwal = old('items', [['menu_id' => '', 'jumlah' => 1]]);
    @endphp

    <div class="page-header">
        <h3 class="page-title">Tambah Pesanan</h3>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Data Pesanan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    @if ($menus->isEmpty())
        <div class="alert alert-warning">
            Belum ada menu yang tersedia, jadi pesanan belum bisa dibuat. Tambahkan atau aktifkan menu dulu di
            <a href="{{ route('admin.menu.index') }}" class="alert-link">Data Menu</a>.
        </div>
    @else
        <form action="{{ route('admin.pesanan.store') }}" method="POST">
            @csrf

            <div class="row">

                {{-- ================= Data pelanggan ================= --}}
                <div class="col-lg-5 grid-margin">
                    <div class="card card-rounded">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Data Pelanggan</h4>

                            <div class="form-group">
                                <label for="nama_pelanggan">Nama Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                                    class="form-control @error('nama_pelanggan') is-invalid @enderror"
                                    value="{{ old('nama_pelanggan') }}" autocomplete="off">
                                @error('nama_pelanggan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="no_whatsapp">Nomor WhatsApp</label>
                                <input type="tel" id="no_whatsapp" name="no_whatsapp"
                                    class="form-control @error('no_whatsapp') is-invalid @enderror"
                                    value="{{ old('no_whatsapp') }}" placeholder="Contoh: 0812-3456-7890"
                                    autocomplete="off">
                                @error('no_whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label for="catatan">Catatan <span class="text-muted">(boleh dikosongkan)</span></label>
                                <textarea id="catatan" name="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                                    placeholder="Contoh: tidak pakai cabai">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= Isi pesanan ================= --}}
                <div class="col-lg-7 grid-margin">
                    <div class="card card-rounded">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Isi Pesanan</h4>

                            {{-- Error umum untuk isi pesanan (misalnya menu sudah tidak tersedia) --}}
                            @error('items')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="row text-muted small mb-1 d-none d-md-flex">
                                <div class="col-md-7">Menu</div>
                                <div class="col-md-3">Jumlah</div>
                            </div>

                            <div id="daftar-item">
                                @foreach ($barisAwal as $i => $baris)
                                    <div class="row align-items-start item-baris mb-2" data-index="{{ $i }}">
                                        <div class="col-md-7 mb-2 mb-md-0">
                                            <select name="items[{{ $i }}][menu_id]"
                                                class="form-select @error('items.' . $i . '.menu_id') is-invalid @enderror">
                                                <option value="" data-harga="0">-- Pilih menu --</option>
                                                @foreach ($menuPerKategori as $namaKategori => $daftarMenu)
                                                    <optgroup label="{{ $namaKategori }}">
                                                        @foreach ($daftarMenu as $menu)
                                                            <option value="{{ $menu->id }}"
                                                                data-harga="{{ $menu->harga }}"
                                                                @selected((string) ($baris['menu_id'] ?? '') === (string) $menu->id)>
                                                                {{ $menu->nama }} - Rp
                                                                {{ number_format($menu->harga, 0, ',', '.') }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @error('items.' . $i . '.menu_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-8 col-md-3">
                                            <input type="number" name="items[{{ $i }}][jumlah]" min="1"
                                                max="99" step="1"
                                                class="form-control @error('items.' . $i . '.jumlah') is-invalid @enderror"
                                                value="{{ $baris['jumlah'] ?? 1 }}">
                                            @error('items.' . $i . '.jumlah')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-4 col-md-2 text-end">
                                            <button type="button" class="btn btn-danger btn-sm hapus-baris text-white"
                                                title="Hapus baris" aria-label="Hapus baris">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" id="tambah-item" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="mdi mdi-plus-circle me-1"></i> Tambah menu
                            </button>

                            <div class="d-flex justify-content-between border-top pt-3 mt-4">
                                <span class="text-muted">Perkiraan subtotal</span>
                                <span class="fw-bold" id="perkiraan-subtotal">Rp 0</span>
                            </div>
                            <p class="small text-muted mt-1 mb-0">
                                Perkiraan saja. Harga, pajak, biaya layanan, dan total dihitung ulang otomatis oleh sistem
                                saat pesanan disimpan.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>

        {{-- Cetakan untuk baris baru. __INDEX__ diganti angka oleh JavaScript di bawah. --}}
        <template id="template-item">
            <div class="row align-items-start item-baris mb-2" data-index="__INDEX__">
                <div class="col-md-7 mb-2 mb-md-0">
                    <select name="items[__INDEX__][menu_id]" class="form-select">
                        <option value="" data-harga="0">-- Pilih menu --</option>
                        @foreach ($menuPerKategori as $namaKategori => $daftarMenu)
                            <optgroup label="{{ $namaKategori }}">
                                @foreach ($daftarMenu as $menu)
                                    <option value="{{ $menu->id }}" data-harga="{{ $menu->harga }}">
                                        {{ $menu->nama }} - Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-8 col-md-3">
                    <input type="number" name="items[__INDEX__][jumlah]" min="1" max="99" step="1"
                        class="form-control" value="1">
                </div>

                <div class="col-4 col-md-2 text-end">
                    <button type="button" class="btn btn-danger btn-sm hapus-baris text-white" title="Hapus baris"
                        aria-label="Hapus baris">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>
        </template>

    @endif

@endsection

@push('scripts')
    <script>
        (function() {
            const daftar = document.getElementById('daftar-item');
            if (!daftar) return; // tidak ada menu tersedia, form tidak ditampilkan

            const cetakan = document.getElementById('template-item');
            const tombolTambah = document.getElementById('tambah-item');
            const labelSubtotal = document.getElementById('perkiraan-subtotal');

            // Nomor baris berikutnya = nomor terbesar yang sudah ada + 1
            let indeks = 0;
            daftar.querySelectorAll('.item-baris').forEach(function(baris) {
                indeks = Math.max(indeks, Number(baris.dataset.index) + 1);
            });

            // Hitung perkiraan subtotal dari harga di pilihan menu (harganya berasal dari database)
            function hitung() {
                let total = 0;
                daftar.querySelectorAll('.item-baris').forEach(function(baris) {
                    const pilih = baris.querySelector('select');
                    const harga = Number(pilih.selectedOptions[0] ? pilih.selectedOptions[0].dataset.harga : 0);
                    const jumlah = Number(baris.querySelector('input[type="number"]').value || 0);
                    total += harga * jumlah;
                });
                labelSubtotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            // Tambah baris menu baru
            tombolTambah.addEventListener('click', function() {
                daftar.insertAdjacentHTML('beforeend', cetakan.innerHTML.replaceAll('__INDEX__', indeks));
                indeks++;
                hitung();
            });

            // Hapus baris (minimal satu baris tetap ada: kalau tinggal satu, isinya dikosongkan)
            daftar.addEventListener('click', function(e) {
                const tombol = e.target.closest('.hapus-baris');
                if (!tombol) return;

                const baris = tombol.closest('.item-baris');
                if (daftar.querySelectorAll('.item-baris').length > 1) {
                    baris.remove();
                } else {
                    baris.querySelector('select').value = '';
                    baris.querySelector('input[type="number"]').value = 1;
                }
                hitung();
            });

            // Hitung ulang setiap kali menu atau jumlah berubah
            daftar.addEventListener('input', hitung);
            daftar.addEventListener('change', hitung);

            hitung();
        })();
    </script>
@endpush
