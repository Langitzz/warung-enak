{{-- Form ganti password, dipakai bareng oleh tampilan semua role.
     Error-nya ada di "bag" bernama 'password' (lihat updatePassword di ProfilController),
     jadi tidak tercampur dengan error form profil di halaman yang sama. --}}

@if (session('success_password'))
    <div class="alert alert-success">
        {{ session('success_password') }}
    </div>
@endif

<form action="{{ route('profil.password') }}" method="POST">
    @csrf
    {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
    @method('PUT')

    <x-input-password name="password_lama" label="Password Lama" autocomplete="current-password" bag="password" />

    <x-input-password name="password" id="password_baru" label="Password Baru" autocomplete="new-password" bag="password"
        hint="Minimal 8 karakter." />

    <x-input-password name="password_confirmation" label="Konfirmasi Password Baru" autocomplete="new-password"
        bag="password" />

    <button type="submit" class="btn btn-primary mt-2">Ganti Password</button>
</form>
