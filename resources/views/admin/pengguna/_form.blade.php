{{-- Isian form pengguna, dipakai bareng oleh create.blade.php dan edit.blade.php.
     Di halaman edit, variabel $pengguna berisi akun yang sedang diedit.
     Di halaman create, $pengguna tidak ada, jadi isian dibiarkan kosong.
     $roles berisi daftar role (kunci = nilai yang disimpan, isi = tulisan di dropdown). --}}

@php
    $edit = isset($pengguna);
    // Admin yang mengedit akunnya sendiri: role dan status dikunci (server juga memaksanya tetap)
    $akunSendiri = $edit && $pengguna->is(auth()->user());
@endphp

<div class="row">

    {{-- Nama --}}
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $pengguna->name ?? '') }}" autocomplete="off">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $pengguna->email ?? '') }}" autocomplete="off">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="row">

    {{-- Nomor WhatsApp --}}
    <div class="col-md-6 mb-3">
        <label for="no_whatsapp" class="form-label">Nomor WhatsApp <span class="text-muted">(boleh
                dikosongkan)</span></label>
        <input type="tel" id="no_whatsapp" name="no_whatsapp"
            class="form-control @error('no_whatsapp') is-invalid @enderror"
            value="{{ old('no_whatsapp', $pengguna->no_whatsapp ?? '') }}" placeholder="Contoh: 0812-3456-7890"
            autocomplete="off">
        @error('no_whatsapp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Role --}}
    <div class="col-md-6 mb-3">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror"
            @disabled($akunSendiri)>
            @foreach ($roles as $kode => $nama)
                <option value="{{ $kode }}" @selected(old('role', $pengguna->role ?? 'kasir') === $kode)>{{ $nama }}</option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if ($akunSendiri)
            <div class="form-text">Kamu tidak bisa mengubah role akunmu sendiri.</div>
        @endif
    </div>

</div>

<div class="row">

    {{-- Password (dengan tombol mata) --}}
    <div class="col-md-6">
        <x-input-password name="password" label="Password" autocomplete="new-password" :hint="$edit
            ? 'Kosongkan kalau tidak ingin mengubah password. Kalau diisi, minimal 8 karakter.'
            : 'Minimal 8 karakter.'" />
    </div>

    {{-- Konfirmasi password --}}
    <div class="col-md-6">
        <x-input-password name="password_confirmation" label="Konfirmasi Password" autocomplete="new-password" />
    </div>

</div>

{{-- Status aktif --}}
<div class="mb-4">
    {{-- Input tersembunyi ini memastikan nilai "0" tetap terkirim saat switch dimatikan --}}
    <input type="hidden" name="aktif" value="0">

    {{-- style inline di bawah untuk memperbaiki bentrok CSS template pada switch --}}
    <div class="form-check form-switch" style="padding-left: 2.5em;">
        <input class="form-check-input" type="checkbox" role="switch" id="aktif" name="aktif" value="1"
            @checked((bool) old('aktif', $pengguna->aktif ?? true)) @disabled($akunSendiri)>
        <label class="form-check-label" for="aktif" style="margin-left: 0;">Aktif (bisa login)</label>
    </div>
    @if ($akunSendiri)
        <div class="form-text">Kamu tidak bisa menonaktifkan akunmu sendiri.</div>
    @else
        <div class="form-text">Matikan untuk mencegah akun ini login tanpa menghapus datanya.</div>
    @endif
</div>

{{-- Tombol --}}
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.pengguna.index') }}" class="btn btn-light">Batal</a>
</div>
