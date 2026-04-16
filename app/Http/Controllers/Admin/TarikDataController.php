<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\PengaturanDapodik;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SyncLog;
use App\Services\DapodikService;
use Illuminate\Http\Request;

class TarikDataController extends Controller
{
    private function getService(): ?DapodikService
    {
        if (!PengaturanDapodik::aktif()) return null;
        return new DapodikService();
    }

    public function index()
    {
        $pengaturan    = PengaturanDapodik::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterAktif = Semester::aktif();
        $totalSiswa    = Siswa::where('is_archived', false)->count();
        $totalGuru     = Guru::where('is_archived', false)->count();
        $totalRombel   = Rombel::where('is_archived', false)->count();
        $totalSemester = Semester::count();
        $lastSync      = SyncLog::latest()->first();
        $syncLogs      = SyncLog::with('user')->latest()->take(10)->get();

        return view('admin.dapodik.tarik', compact(
            'pengaturan', 'semesters', 'semesterAktif',
            'totalSiswa', 'totalGuru', 'totalRombel',
            'totalSemester', 'lastSync', 'syncLogs'
        ));
    }

    public function tarikSemua(Request $request)
    {
        $request->validate(['semester_id' => 'required']);

        $service = $this->getService();
        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Pengaturan Dapodik belum dikonfigurasi!']);
        }

        try {
            $result = $service->syncSemua($request->semester_id);

            Semester::query()->update(['is_aktif' => false]);
            Semester::where('semester_id', $request->semester_id)->update(['is_aktif' => true]);

            return response()->json([
                'status'  => true,
                'message' => 'Semua data berhasil disinkronkan!',
                'detail'  => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tarikKategori(Request $request)
    {
        $request->validate([
            'jenis'       => 'required|in:semester,siswa,guru,rombel',
            'semester_id' => $request->jenis === 'semester' ? 'nullable' : 'required',
        ]);

        $service = $this->getService();
        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Pengaturan Dapodik belum dikonfigurasi!']);
        }

        try {
            $jenis  = $request->jenis;
            $result = match($jenis) {
                'semester' => $service->syncSemester(),
                'siswa'    => $service->syncSiswa($request->semester_id),
                'guru'     => $service->syncGuru($request->semester_id),
                'rombel'   => $service->syncRombel($request->semester_id),
            };

            // Update semester aktif
            if ($request->semester_id) {
                Semester::query()->update(['is_aktif' => false]);
                Semester::where('semester_id', $request->semester_id)->update(['is_aktif' => true]);
            }

            // Kalau tarik semester, otomatis set semester terbaru jadi aktif
            if ($jenis === 'semester') {
                $latest = Semester::orderByDesc('semester_id')->first();
                if ($latest) {
                    Semester::query()->update(['is_aktif' => false]);
                    $latest->update(['is_aktif' => true]);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data ' . $jenis . ' berhasil disinkronkan!',
                'detail'  => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function setSemesterAktif(Request $request)
    {
        $request->validate(['semester_id' => 'required']);
        Semester::query()->update(['is_aktif' => false]);
        Semester::where('semester_id', $request->semester_id)->update(['is_aktif' => true]);

        return response()->json(['status' => true, 'message' => 'Semester aktif berhasil diubah!']);
    }
}
