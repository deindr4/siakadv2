<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'name', 'role', 'action', 'module',
        'description', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper untuk log aktivitas ────────────────────────
    public static function log(
        string $action,
        string $module,
        string $description,
        ?object $model = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        try {
            $user = Auth::user();

            static::create([
                'user_id'     => $user?->id,
                'name'        => $user?->name,
                'role'        => $user?->getRoleNames()->first(),
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'model_type'  => $model ? get_class($model) : null,
                'model_id'    => $model?->id,
                'old_values'  => !empty($oldValues) ? $oldValues : null,
                'new_values'  => !empty($newValues) ? $newValues : null,
                'ip_address'  => Request::ip(),
                'user_agent'  => substr(Request::userAgent() ?? '', 0, 255),
            ]);
        } catch (\Exception $e) {
            // Jangan sampai log error menghentikan proses utama
            \Log::error('ActivityLog error: ' . $e->getMessage());
        }
    }

    // ── Label & Warna action ─────────────────────────────────────
    public function actionLabel(): string
    {
        return match($this->action) {
            'create'  => 'Tambah',
            'update'  => 'Edit',
            'delete'  => 'Hapus',
            'login'   => 'Login',
            'logout'  => 'Logout',
            'export'  => 'Export',
            'import'  => 'Import',
            'approve' => 'Setujui',
            'reject'  => 'Tolak',
            'restore' => 'Restore',
            default   => ucfirst($this->action),
        };
    }

    public function actionColor(): string
    {
        return match($this->action) {
            'create'  => '#16a34a',
            'update'  => '#d97706',
            'delete'  => '#dc2626',
            'login'   => '#6366f1',
            'logout'  => '#94a3b8',
            'export'  => '#0284c7',
            'import'  => '#0284c7',
            'approve' => '#16a34a',
            'reject'  => '#dc2626',
            default   => '#64748b',
        };
    }

    public function actionBg(): string
    {
        return match($this->action) {
            'create'  => '#dcfce7',
            'update'  => '#fef3c7',
            'delete'  => '#fee2e2',
            'login'   => '#eef2ff',
            'logout'  => '#f1f5f9',
            'export'  => '#e0f2fe',
            'import'  => '#e0f2fe',
            'approve' => '#dcfce7',
            'reject'  => '#fee2e2',
            default   => '#f1f5f9',
        };
    }
}
