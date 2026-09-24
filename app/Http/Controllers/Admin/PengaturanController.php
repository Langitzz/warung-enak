<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    // Zona waktu yang boleh dipilih (kunci = nilai yang disimpan, isi = tulisan di dropdown)
    private const ZONA_WAKTU = [
        'Asia/Jakarta' => 'WIB (Asia/Jakarta)',
        'Asia/Makassar' => 'WITA (Asia/Makassar)',
        'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
    ];

    // ================= Profil Warung =================

    // Halaman form profil warung
    public function profil()
    {
        $pengaturan = Pengaturan::ambil();

        return view('admin.pengaturan.profil', compact('pengaturan'));
    }

    // Simpan profil warung
    public function updateProfil(Request $request)
    {
        $data = $request->validate(
            [
                'nama_warung' => ['required', 'string', 'max:100'],
                'telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
                'alamat' => ['nullable', 'string', 'max:255'],
                'jam_buka' => ['nullable', 'date_format:H:i'],
                'jam_tutup' => ['nullable', 'date_format:H:i'],
                'deskripsi' => ['nullable', 'string', 'max:500'],
                'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'nama_warung.required' => 'Nama warung wajib diisi.',
                'nama_warung.max' => 'Nama warung maksimal 100 karakter.',
                'telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, dan tanda + - ( ).',
                'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
                'jam_buka.date_format' => 'Format jam buka tidak valid.',
                'jam_tutup.date_format' => 'Format jam tutup tidak valid.',
                'logo.image' => 'Logo harus berupa gambar.',
                'logo.mimes' => 'Logo harus berformat jpg, jpeg, png, atau webp.',
                'logo.max' => 'Ukuran logo maksimal 2 MB.',
            ]
        );

        $pengaturan = Pengaturan::ambil();

        if ($request->hasFile('logo')) {
            // Ada logo baru: hapus logo lama kalau ada, lalu simpan yang baru
            if ($pengaturan->logo) {
                Storage::disk('public')->delete($pengaturan->logo);
            }
            $data['logo'] = $request->file('logo')->store('logo', 'public');
        } elseif ($request->boolean('hapus_logo')) {
            // Tidak ada logo baru, tapi kotak "Hapus logo" dicentang: hapus logo lama
            if ($pengaturan->logo) {
                Storage::disk('public')->delete($pengaturan->logo);
            }
            $data['logo'] = null;
        } else {
            // Tidak ada perubahan pada logo: jangan sentuh logo yang sudah tersimpan
            unset($data['logo']);
        }

        $pengaturan->update($data);

        return redirect()
            ->route('admin.pengaturan.profil')
            ->with('success', 'Profil warung berhasil disimpan.');
    }

    // ================= Pengaturan Sistem =================

    // Halaman form pengaturan sistem
    public function sistem()
    {
        $pengaturan = Pengaturan::ambil();
        $zonaWaktu = self::ZONA_WAKTU;

        return view('admin.pengaturan.sistem', compact('pengaturan', 'zonaWaktu'));
    }

    // Simpan pengaturan sistem
    public function updateSistem(Request $request)
    {
        $data = $request->validate(
            [
                'zona_waktu' => ['required', Rule::in(array_keys(self::ZONA_WAKTU))],
                'pajak' => ['required', 'integer', 'between:0,100'],
                'biaya_layanan' => ['required', 'integer', 'between:0,100'],
            ],
            [
                'zona_waktu.in' => 'Zona waktu yang dipilih tidak valid.',
                'pajak.required' => 'Pajak wajib diisi (isi 0 kalau tidak ada).',
                'pajak.between' => 'Pajak harus antara 0 sampai 100 persen.',
                'biaya_layanan.required' => 'Biaya layanan wajib diisi (isi 0 kalau tidak ada).',
                'biaya_layanan.between' => 'Biaya layanan harus antara 0 sampai 100 persen.',
            ]
        );

        // Switch yang dimatikan tidak ikut terkirim, jadi dicek manual
        $data['terima_pesanan'] = $request->boolean('terima_pesanan');
        $data['notif_pesanan'] = $request->boolean('notif_pesanan');

        Pengaturan::ambil()->update($data);

        return redirect()
            ->route('admin.pengaturan.sistem')
            ->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
