<?php

namespace App\Http\Controllers;

use App\Models\SaranCuti;
use Illuminate\Http\Request;

class SaranController extends Controller
{
    public function index(Request $request)
    {
        $saranCutis = SaranCuti::with('pegawai')
            ->when($request->q, function ($query, $q) {
                $query->where('nip', 'like', "%{$q}%")
                    ->orWhereHas('pegawai', fn ($w) => $w->where('nama', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('saran.index', compact('saranCutis'));
    }
}
