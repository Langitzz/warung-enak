{{-- Isian form kategori, dipakai bareng oleh create.blade.php dan edit.blade.php.
     Di halaman edit, variabel $kategori berisi data kategori yang sedang diedit.
     Di halaman create, $kategori tidak ada, jadi isian dibiarkan kosong. --}}

{{-- Nama --}}
<div class="mb-3">
    <label for="nama" class="form-label">Nama Kategori</label>
    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
        value="{{ old('nama', $kategori->nama ?? '') }}" placeholder="Contoh: Nasi Goreng">
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Status aktif --}}
<div class="mb-4">
    {{-- Input tersembunyi ini memastikan nilai "0" tetap terkirim saat switch dimatikan --}}
    <input type="hidden" name="aktif" value="0">

    {{-- style inline di bawah untuk memperbaiki bentrok CSS template pada switch --}}
    <div class="form-check form-switch" style="padding-left: 2.5em;">
        <input class="form-check-input" type="checkbox" role="switch" id="aktif" name="aktif" value="1"
            @checked((bool) old('aktif', $kategori->aktif ?? true))>
        <label class="form-check-label" for="aktif" style="margin-left: 0;">Aktif (bisa dipilih saat menambah
            menu)</label>
    </div>
    <div class="form-text">Matikan kalau kategori ini belum rilis.</div>
</div>

{{-- Tombol --}}
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-light">Batal</a>
</div>
