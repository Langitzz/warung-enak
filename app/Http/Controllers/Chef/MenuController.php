<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class MenuController extends Controller
{
    // Read-only: Chef lihat semua menu (termasuk yang tidak tersedia) buat referensi dapur
    public function index()
    {
        $menus = Menu::with('kategori')->orderBy('nama')->get();

        return view('chef.menu.index', compact('menus'));
    }
}