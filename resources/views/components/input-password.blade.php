{{-- Kolom password dengan tombol mata (lihat/sembunyikan).
     Cara pakai:
       <x-input-password name="password" label="Password" autocomplete="new-password" />
     Opsi:
       id        : id kolom (bawaan sama dengan name)
       bag       : nama "bag" error kalau errornya bukan yang bawaan (contoh: 'password')
       hint      : teks kecil di bawah kolom --}}

@props(['name', 'label', 'id' => null, 'autocomplete' => 'current-password', 'bag' => 'default', 'hint' => null])

@php
    $id = $id ?? $name;
    $pesan = $errors->getBag($bag)->first($name);
@endphp

<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>

    {{-- has-validation: pesan error ditaruh di dalam input-group supaya tetap tampil --}}
    <div class="input-group has-validation">
        <input type="password" id="{{ $id }}" name="{{ $name }}"
            class="form-control @if ($pesan) is-invalid @endif" autocomplete="{{ $autocomplete }}">
        <button type="button" class="btn btn-outline-secondary" data-toggle-password="{{ $id }}"
            aria-label="Lihat atau sembunyikan password" tabindex="-1">
            <i class="mdi mdi-eye-outline"></i>
        </button>
        @if ($pesan)
            <div class="invalid-feedback">{{ $pesan }}</div>
        @endif
    </div>

    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>

{{-- Script tombol mata: dimuat sekali saja walau komponen dipakai berkali-kali --}}
@once
    @push('scripts')
        <script>
            document.addEventListener('click', function(e) {
                const tombol = e.target.closest('[data-toggle-password]');
                if (!tombol) return;

                const input = document.getElementById(tombol.dataset.togglePassword);
                const ikon = tombol.querySelector('i');
                const tampil = input.type === 'password';

                input.type = tampil ? 'text' : 'password';
                ikon.classList.toggle('mdi-eye-outline', !tampil);
                ikon.classList.toggle('mdi-eye-off-outline', tampil);
            });
        </script>
    @endpush
@endonce
