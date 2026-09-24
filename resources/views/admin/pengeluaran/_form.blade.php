{{-- Isian form pengeluaran, dipakai bareng oleh create.blade.php dan edit.blade.php.
     Di halaman edit, variabel $pengeluaran berisi data yang sedang diedit. --}}

<div class="mb-3">
    <label for="tanggal" class="form-label">Tanggal</label>
    <input type="date" id="tanggal" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
        value="{{ old('tanggal', isset($pengeluaran) ? $pengeluaran->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}">
    @error('tanggal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="keterangan" class="form-label">Keterangan</label>
    <input type="text" id="keterangan" name="keterangan"
        class="form-control @error('keterangan') is-invalid @enderror"
        value="{{ old('keterangan', $pengeluaran->keterangan ?? '') }}" placeholder="Contoh: Beli beras 10kg">
    @error('keterangan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="jumlah" class="form-label">Jumlah (Rp)</label>
    <input type="number" id="jumlah" name="jumlah" min="1"
        class="form-control @error('jumlah') is-invalid @enderror"
        value="{{ old('jumlah', $pengeluaran->jumlah ?? '') }}" placeholder="Contoh: 50000">
    @error('jumlah')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.pengeluaran.index') }}" class="btn btn-light">Batal</a>
</div>
