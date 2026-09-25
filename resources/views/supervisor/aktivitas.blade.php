@extends('layouts.admin')

@section('title', 'Aktivitas Operasional')

@section('content')

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-1">Aktivitas Operasional</h4>
            <p class="text-muted mb-4">Riwayat aktivitas pesanan di seluruh sistem — dibuat, diproses, dibatalkan.</p>

            @forelse ($aktivitas as $item)
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span>{{ $item->deskripsi }}</span>
                    <span class="text-muted small">
                        {{ $item->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada aktivitas tercatat.</p>
            @endforelse

            <div class="mt-3">
                {{ $aktivitas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection