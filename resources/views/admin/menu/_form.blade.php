{{-- Isian form menu, dipakai bareng oleh create.blade.php dan edit.blade.php.
     Di halaman edit, variabel $menu berisi data menu yang sedang diedit.
     Di halaman create, $menu tidak ada, jadi isian dibiarkan kosong. --}}

{{-- Nama --}}
<div class="mb-3">
    <label for="nama" class="form-label">Nama Menu</label>
    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
        value="{{ old('nama', $menu->nama ?? '') }}" placeholder="Contoh: Nasi Goreng Spesial">
    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Kategori (dropdown dari tabel kategoris) --}}
<div class="mb-3">
    <label for="kategori_id" class="form-label">Kategori</label>
    <select id="kategori_id" name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror">
        <option value="">-- Pilih kategori --</option>
        @foreach ($kategoris as $kategori)
            <option value="{{ $kategori->id }}" @selected(old('kategori_id', $menu->kategori_id ?? '') == $kategori->id)>
                {{ $kategori->nama }}
            </option>
        @endforeach
    </select>
    @error('kategori_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($kategoris->isEmpty())
        <div class="form-text text-danger">Belum ada kategori aktif. Tambahkan dulu di halaman Kategori Menu.</div>
    @endif
</div>

{{-- Harga --}}
<div class="mb-3">
    <label for="harga" class="form-label">Harga (Rp)</label>
    <input type="number" id="harga" name="harga" min="0" step="1"
        class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $menu->harga ?? '') }}"
        placeholder="Contoh: 18000">
    @error('harga')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Deskripsi --}}
<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi <span class="text-muted">(boleh dikosongkan)</span></label>
    <textarea id="deskripsi" name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror"
        placeholder="Contoh: Nasi goreng dengan telur, ayam suwir, dan kerupuk">{{ old('deskripsi', $menu->deskripsi ?? '') }}</textarea>
    @error('deskripsi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Foto --}}
<div class="mb-3">
    <label for="foto" class="form-label">Foto <span class="text-muted">(boleh dikosongkan, maks 2 MB)</span></label>

    {{-- Di halaman edit: tampilkan foto yang sekarang kalau ada --}}
    @if (isset($menu) && $menu->foto)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}"
                style="width: 96px; height: 96px; object-fit: cover; border-radius: 8px;">
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

{{-- Status tersedia --}}
<div class="mb-4">
    {{-- Input tersembunyi ini memastikan nilai "0" tetap terkirim saat switch dimatikan --}}
    <input type="hidden" name="tersedia" value="0">
    <div class="form-check form-switch" style="padding-left: 2.5em;">
        <input class="form-check-input" type="checkbox" role="switch" id="tersedia" name="tersedia" value="1"
            @checked((bool) old('tersedia', $menu->tersedia ?? true))>
        <label class="form-check-label" for="tersedia" style="margin-left: 0;">Tersedia (tampil dan bisa
            dipesan)</label>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.menu.index') }}" class="btn btn-light">Batal</a>
</div>
