<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action'))  $query->where('action', $request->action);
        if ($request->filled('module'))  $query->where('module', $request->module);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%'.$request->search.'%')
                  ->orWhere('name', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('tgl_dari')) {
            $query->whereDate('created_at', '>=', $request->tgl_dari);
        }
        if ($request->filled('tgl_sampai')) {
            $query->whereDate('created_at', '<=', $request->tgl_sampai);
        }

        $logs    = $query->paginate(50)->withQueryString();
        $modules = ActivityLog::distinct()->pluck('module')->filter()->sort();
        $actions = ActivityLog::distinct()->pluck('action')->filter()->sort();
        $total   = ActivityLog::count();

        // Stats
        $stats = (object)[
            'total'   => $total,
            'hari_ini'=> ActivityLog::whereDate('created_at', today())->count(),
            'login'   => ActivityLog::where('action', 'login')->whereDate('created_at', today())->count(),
            'delete'  => ActivityLog::where('action', 'delete')->whereDate('created_at', today())->count(),
        ];

        return view('admin.activity-log.index', compact(
            'logs', 'modules', 'actions', 'stats', 'total'
        ));
    }

    // ── Hapus semua log ──────────────────────────────────────────
    public function destroyAll(Request $request)
    {
        $request->validate([
            'konfirmasi' => 'required|in:HAPUS SEMUA',
        ], [
            'konfirmasi.in' => 'Ketik "HAPUS SEMUA" untuk konfirmasi.',
        ]);

        $count = ActivityLog::count();
        ActivityLog::truncate();

        // Log aksi hapus ini sendiri
        ActivityLog::log('delete', 'activity_log', "Menghapus {$count} log aktivitas");

        return back()->with('success', "{$count} log aktivitas berhasil dihapus!");
    }

    // ── Hapus log lebih dari N hari ──────────────────────────────
    public function destroyOld(Request $request)
    {
        $request->validate([
            'hari' => 'required|integer|min:7|max:365',
        ]);

        $count = ActivityLog::where('created_at', '<', now()->subDays($request->hari))->count();
        ActivityLog::where('created_at', '<', now()->subDays($request->hari))->delete();

        ActivityLog::log('delete', 'activity_log', "Menghapus {$count} log aktivitas lebih dari {$request->hari} hari");

        return back()->with('success', "{$count} log lama (>{$request->hari} hari) berhasil dihapus!");
    }

    // ── Download log sebagai Excel ───────────────────────────────
    public function download(Request $request)
    {
        $query = ActivityLog::latest();
        if ($request->filled('tgl_dari'))   $query->whereDate('created_at', '>=', $request->tgl_dari);
        if ($request->filled('tgl_sampai')) $query->whereDate('created_at', '<=', $request->tgl_sampai);
        if ($request->filled('action'))     $query->where('action', $request->action);
        if ($request->filled('module'))     $query->where('module', $request->module);

        $logs = $query->get();

        // Simple CSV download
        $filename = 'activity-log-' . date('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Tanggal', 'User', 'Role', 'Aksi', 'Modul', 'Deskripsi', 'IP Address']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->name ?? '-',
                    $log->role ?? '-',
                    $log->actionLabel(),
                    $log->module,
                    $log->description,
                    $log->ip_address ?? '-',
                ]);
            }
            fclose($file);
        };

        ActivityLog::log('export', 'activity_log', 'Download log aktivitas');

        return response()->stream($callback, 200, $headers);
    }
}
