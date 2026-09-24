{{-- Form profil akun, dipakai bareng oleh tampilan semua role (admin, kasir, pelanggan).
     Variabel $user berisi akun yang sedang login. --}}

{{-- enctype="multipart/form-data" wajib supaya file foto bisa terkirim --}}
<form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- Browser cuma bisa kirim GET/POST, jadi method PATCH "dipinjam" lewat baris ini --}}
    @method('PATCH')

    {{-- Nama --}}
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name) }}" autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email) }}" autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Nomor WhatsApp --}}
    <div class="mb-3">
        <label for="no_whatsapp" class="form-label">Nomor WhatsApp <span class="text-muted">(boleh
                dikosongkan)</span></label>
        <input type="tel" id="no_whatsapp" name="no_whatsapp"
            class="form-control @error('no_whatsapp') is-invalid @enderror"
            value="{{ old('no_whatsapp', $user->no_whatsapp) }}" placeholder="Contoh: 0812-3456-7890"
            autocomplete="tel">
        @error('no_whatsapp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Foto profil --}}
    <div class="mb-4">
        <label for="foto" class="form-label">Foto Profil <span class="text-muted">(boleh dikosongkan, maks 2MB)</span></label>

        {{-- Tampilkan foto yang sekarang kalau ada --}}
        @if ($user->foto)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto {{ $user->name }}"
                    style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%;">
                <p class="small text-muted mb-0">Foto saat ini. Pilih file baru kalau mau menggantinya.</p>

                {{-- style inline di bawah untuk memperbaiki bentrok CSS template pada checkbox --}}
                <div class="form-check mt-2" style="padding-left: 1.5em;">
                    <input class="form-check-input" type="checkbox" id="hapus_foto" name="hapus_foto" value="1">
                    <label class="form-check-label" for="hapus_foto" style="margin-left: 0;">Hapus foto ini</label>
                </div>
            </div>
        @endif

        <input type="file" id="foto" name="foto" accept="image/*"
            class="form-control @error('foto') is-invalid @enderror">
        @error('foto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
</form>
