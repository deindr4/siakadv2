<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allAsArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah'  => 'required|string|max:255',
            'npsn'          => 'nullable|string|max:20',
            'nss'           => 'nullable|string|max:30',
            'alamat'        => 'nullable|string',
            'kelurahan'     => 'nullable|string',
            'kecamatan'     => 'nullable|string',
            'kabupaten'     => 'nullable|string',
            'provinsi'      => 'nullable|string',
            'kode_pos'      => 'nullable|string|max:10',
            'telepon'       => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'website'       => 'nullable|string|max:100',
            'logo'          => 'nullable|image|max:2048',
            'kop_surat'     => 'nullable|image|max:5120',
            'kop_mode'      => 'nullable|in:auto,image',
        ]);

        // Simpan field teks
        $fields = [
            'nama_sekolah', 'npsn', 'nss', 'alamat',
            'kelurahan', 'kecamatan', 'kabupaten', 'provinsi',
            'kode_pos', 'telepon', 'email', 'website',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field));
        }

        // Simpan mode kop
        Setting::set('kop_mode', $request->input('kop_mode', 'auto'));

        // Upload logo sekolah
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $path);
        }

        // Upload kop surat gambar
        if ($request->hasFile('kop_surat')) {
            $oldKop = Setting::get('kop_surat');
            if ($oldKop && Storage::disk('public')->exists($oldKop)) {
                Storage::disk('public')->delete($oldKop);
            }
            $path = $request->file('kop_surat')->store('settings', 'public');
            Setting::set('kop_surat', $path);
        }

        return back()->with('success', 'Pengaturan sekolah berhasil disimpan!');
    }
}
