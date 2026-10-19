{{-- Form ganti password, dipakai bareng oleh tampilan semua role.
     Error-nya ada di "bag" bernama 'password' (lihat updatePassword di ProfilController),
     jadi tidak tercampur dengan error form profil di halaman yang sama. --}}

@if (session('success_password'))
    <div class="alert alert-success">
        {{ session('success_password') }}
    </div>
@endif

<form action="{{ route('profil.password') }}" method="POST" id="formGantiPassword">
    @csrf
    {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
    @method('PUT')

    <x-input-password name="password_lama" label="Password Lama" autocomplete="current-password" bag="password" />

    <x-input-password name="password" id="password_baru" label="Password Baru" autocomplete="new-password" bag="password"
        hint="Minimal 8 karakter." />

    <x-input-password name="password_confirmation" label="Konfirmasi Password Baru" autocomplete="new-password"
        bag="password" />

    <button type="submit" class="btn btn-primary mt-2" id="tombolGantiPassword">Ganti Password</button>
</form>

@push('scripts')
    <script>
        (function() {
            const form = document.getElementById('formGantiPassword');
            const tombol = document.getElementById('tombolGantiPassword');
            const teksAsli = tombol.innerHTML;

            form.addEventListener('submit', function() {
                tombol.disabled = true;
                tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            });

            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    tombol.disabled = false;
                    tombol.innerHTML = teksAsli;
                }
            });
        })();
    </script>
@endpush