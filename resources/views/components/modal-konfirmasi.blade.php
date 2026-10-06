{{-- Modal konfirmasi pengganti confirm() bawaan browser, tampil di tengah layar.
     Cara pakai: di tag <form>, ganti onsubmit="return confirm('...')" jadi
     data-confirm="tulisan pertanyaannya di sini". --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 48px;"></i>
                <p class="mt-3 mb-0" id="modalKonfirmasiPesan"></p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4" id="modalKonfirmasiYa">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const modalEl = document.getElementById('modalKonfirmasi');
                const modal = new bootstrap.Modal(modalEl);
                const pesanEl = document.getElementById('modalKonfirmasiPesan');
                const tombolYa = document.getElementById('modalKonfirmasiYa');
                let formAktif = null;

                document.addEventListener('submit', function (e) {
                    const form = e.target;
                    if (!form.matches('form[data-confirm]')) return;
                    if (form.dataset.confirmed === '1') return; // sudah dikonfirmasi, biarkan lanjut submit

                    e.preventDefault();
                    formAktif = form;
                    pesanEl.textContent = form.dataset.confirm;
                    tombolYa.textContent = form.dataset.confirmYa || 'Ya, Lanjutkan';
                    modal.show();
                });

                tombolYa.addEventListener('click', function () {
                    if (!formAktif) return;
                    modal.hide();
                    formAktif.dataset.confirmed = '1';
                    formAktif.submit();
                });
            })();
        </script>
    @endpush
@endonce