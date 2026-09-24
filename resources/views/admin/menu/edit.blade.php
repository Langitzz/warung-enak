@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit Menu</h3>
        <p class="text-muted mb-0">Ubah data menu "{{ $menu->nama }}".</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-rounded">
                <div class="card-body">
                    {{-- enctype="multipart/form-data" wajib supaya file foto bisa terkirim --}}
                    <form action="{{ route('admin.menu.update', $menu) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
                        @method('PUT')
                        @include('admin.menu._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
