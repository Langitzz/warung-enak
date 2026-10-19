{{-- Toast notifikasi + AJAX buat tombol "Pesan"/"Tambah ke Keranjang" — dipakai bareng
     di landing page dan halaman /menu, biar perilakunya konsisten di dua-duanya. --}}

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="toastKeranjang" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastKeranjangPesan"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const elToastKeranjang = document.getElementById('toastKeranjang');
        const toastKeranjangBootstrap = new bootstrap.Toast(elToastKeranjang, { delay: 3000 });

        function tampilkanToastKeranjang(pesan, jenis) {
            elToastKeranjang.classList.remove('bg-success', 'bg-danger');
            elToastKeranjang.classList.add(jenis === 'error' ? 'bg-danger' : 'bg-success');
            document.getElementById('toastKeranjangPesan').textContent = pesan;
            toastKeranjangBootstrap.show();
        }

        document.querySelectorAll('.form-tambah-keranjang').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const tombol = form.querySelector('button');
                const teksAsli = tombol.innerHTML;
                tombol.disabled = true;
                tombol.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menambahkan ke keranjang.');
                        return data;
                    })
                    .then((data) => {
                        tampilkanToastKeranjang(data.message, 'success');

                        const badge = document.getElementById('badgeKeranjang');
                        if (badge) {
                            badge.textContent = data.jumlah_keranjang;
                            badge.classList.remove('d-none');
                            badge.style.animation = 'none';
                            void badge.offsetWidth;
                            badge.style.animation = 'munculBadge .25s ease';
                        }
                    })
                    .catch((err) => {
                        tampilkanToastKeranjang(err.message, 'error');
                    })
                    .finally(() => {
                        tombol.disabled = false;
                        tombol.innerHTML = teksAsli;
                    });
            });
        });
    </script>
@endpush