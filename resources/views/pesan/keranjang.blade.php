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
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item['menu']->nama }}</div>
                                        <div class="small text-muted">Rp
                                            {{ number_format($item['menu']->harga, 0, ',', '.') }}/porsi</div>
                                    </td>
                                    <td style="max-width: 140px;">
                                        <form action="{{ route('keranjang.ubah', $item['menu']) }}" method="POST"
                                            class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="jumlah" value="{{ $item['jumlah'] }}"
                                                min="1" max="50"
                                                class="form-control form-control-sm text-center">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Ubah</button>
                                        </form>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('keranjang.hapus', $item['menu']) }}" method="POST">
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
                                <td class="text-end">Rp {{ number_format($total, 0, ',', '.') }}</td>
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
    </section>

@endsection
