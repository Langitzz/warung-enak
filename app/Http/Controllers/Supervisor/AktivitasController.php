<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;

class AktivitasController extends Controller
{
    public function index()
    {
        $aktivitas = Aktivitas::latest()->latest('id')->paginate(20);

        return view('supervisor.aktivitas', compact('aktivitas'));
    }
}