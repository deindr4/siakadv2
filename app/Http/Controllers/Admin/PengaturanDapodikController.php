<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanDapodik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PengaturanDapodikController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanDapodik::latest()->first();
        return view('admin.dapodik.pengaturan', compact('pengaturan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address'   => 'required',
            'port'         => 'required',
            'npsn'         => 'required',
            'bearer_token' => 'required',
        ], [
            'ip_address.required'   => 'IP Address wajib diisi',
            'port.required'         => 'Port wajib diisi',
            'npsn.required'         => 'NPSN wajib diisi',
            'bearer_token.required' => 'Bearer Token wajib diisi',
        ]);

        // Nonaktifkan semua pengaturan lama
        PengaturanDapodik::query()->update(['is_active' => false]);

        // Simpan pengaturan baru
        PengaturanDapodik::create([
            'ip_address'   => $request->ip_address,
            'port'         => $request->port,
            'npsn'         => $request->npsn,
            'bearer_token' => $request->bearer_token,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.dapodik.pengaturan')
            ->with('success', 'Pengaturan Dapodik berhasil disimpan!');
    }

    public function testKoneksi()
    {
        $pengaturan = PengaturanDapodik::aktif();

        if (!$pengaturan) {
            return response()->json([
                'status'  => false,
                'message' => 'Pengaturan Dapodik belum dikonfigurasi!'
            ]);
        }

        try {
            $url = $pengaturan->base_url
                 . '/WebService/getSekolah?npsn='
                 . $pengaturan->npsn;

            $response = Http::timeout(10)
                ->withToken($pengaturan->bearer_token)
                ->get($url);

            if ($response->successful()) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Koneksi ke Dapodik berhasil! ✅'
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Koneksi gagal! Response: ' . $response->status()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Koneksi gagal! ' . $e->getMessage()
            ]);
        }
    }
}
