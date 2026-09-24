@extends('layouts.admin')

@section('title', 'Pesanan Masuk')

@section('content')

    @include('partials.admin-alert')

    @php
        $tombolStatus = [
            'diproses' => ['Mulai Proses', 'btn-info text-white'],
            'siap' => ['Tandai Siap', 'btn-primary'],
        ];
    @endphp

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-1">Pesanan Masuk</h4>
            <p class="text-muted mb-4">Antrean dapur, yang paling lama masuk duluan.</p>

            @forelse ($pesanans as $pesanan)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="fw-bold">{{ $pesanan->kode }}</span>
                            <span class="badge badge-secondary ms-2">{{ $pesanan->label_status }}</span>
                            <div class="small text-muted">
                                {{ $pesanan->nama_pelanggan }} &bull;
                                {{ $pesanan->created_at->locale('id')->translatedFormat('H:i') }}
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            @foreach (array_intersect($pesanan->statusBerikutnya(), ['diproses', 'siap']) as $statusBaru)
                                <form action="{{ route('chef.pesanan.status', $pesanan) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $statusBaru }}">
                                    <button type="submit" class="btn btn-sm {{ $tombolStatus[$statusBaru][1] }}">
                                        {{ $tombolStatus[$statusBaru][0] }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                    <ul class="mb-0 mt-2 small">
                        @foreach ($pesanan->items as $item)
                            <li>{{ $item->nama_menu }} &times; {{ $item->jumlah }}</li>
                        @endforeach
                    </ul>
                    @if ($pesanan->catatan)
                        <div class="small fst-italic mt-1">Catatan: {{ $pesanan->catatan }}</div>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">Tidak ada pesanan yang perlu dikerjakan saat ini. 🎉</p>
            @endforelse

            <div class="mt-3">
                {{ $pesanans->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection