<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user  = $request->user();
        $role  = $user->getRoleNames()->first();
        $siswa = null;
        $guru  = null;

        if ($role === 'siswa') {
            $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();
        } elseif (in_array($role, ['guru','admin','bk','tata_usaha','kepala_sekolah','wakil_kepala_sekolah'])) {
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
        }

        return view('profile.edit', compact('user','role','siswa','guru'));
    }
}
