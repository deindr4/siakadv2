<?php
// ============================================================
// app/Models/Tiket.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Tiket extends Model
{
    use SoftDeletes;

    protected $table = 'tikets';
    protected $fillable = [
        'user_id', 'role_pembuat', 'is_anonim',
        'judul', 'kategori', 'kategori_lainnya', 'isi',
        'foto', 'foto_original',
        'status', 'prioritas',
        'last_response_at', 'locked_at', 'locked_by',
        'unlocked_by', 'unlocked_at', 'alasan_unlock',
        'closed_by', 'closed_at',
    ];

    protected $casts = [
        'is_anonim'        => 'boolean',
        'last_response_at' => 'datetime',
        'locked_at'        => 'datetime',
        'unlocked_at'      => 'datetime',
        'closed_at'        => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────
    public function user()       { return $this->belongsTo(User::class); }
    public function lockedBy()   { return $this->belongsTo(User::class, 'locked_by'); }
    public function unlockedBy() { return $this->belongsTo(User::class, 'unlocked_by'); }
    public function closedBy()   { return $this->belongsTo(User::class, 'closed_by'); }
    public function respon()     { return $this->hasMany(TiketRespon::class)->orderBy('created_at'); }

    // ── Helpers ───────────────────────────────────────────
    public static function kategoriList(): array
    {
        return [
            'kritik'                   => 'Kritik',
            'saran'                    => 'Saran',
            'pengaduan_fasilitas'      => 'Pengaduan Fasilitas',
            'pengaduan_guru_staff'     => 'Pengaduan Guru/Staff',
            'pengaduan_teman_bullying' => 'Pengaduan Teman/Bullying',
            'lainnya'                  => 'Lainnya',
        ];
    }

    public function kategoriLabel(): string
    {
        if ($this->kategori === 'lainnya' && $this->kategori_lainnya) {
            return $this->kategori_lainnya;
        }
        return self::kategoriList()[$this->kategori] ?? ucfirst($this->kategori);
    }

    public function kategoriColor(): string
    {
        return match($this->kategori) {
            'kritik'                   => '#dc2626',
            'saran'                    => '#16a34a',
            'pengaduan_fasilitas'      => '#d97706',
            'pengaduan_guru_staff'     => '#7c3aed',
            'pengaduan_teman_bullying' => '#0284c7',
            'lainnya'                  => '#64748b',
            default                    => '#374151',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'open'      => 'Terbuka',
            'diproses'  => 'Diproses',
            'selesai'   => 'Selesai',
            'terkunci'  => 'Terkunci',
            default     => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'open'     => '#16a34a',
            'diproses' => '#0284c7',
            'selesai'  => '#64748b',
            'terkunci' => '#dc2626',
            default    => '#374151',
        };
    }

    public function statusBg(): string
    {
        return match($this->status) {
            'open'     => '#dcfce7',
            'diproses' => '#e0f2fe',
            'selesai'  => '#f1f5f9',
            'terkunci' => '#fee2e2',
            default    => '#f8fafc',
        };
    }

    public function prioritasColor(): string
    {
        return match($this->prioritas) {
            'tinggi' => '#dc2626',
            'sedang' => '#d97706',
            'rendah' => '#16a34a',
            default  => '#64748b',
        };
    }

    public function isLocked(): bool { return $this->status === 'terkunci'; }

    public function isClosed(): bool { return in_array($this->status, ['selesai', 'terkunci']); }

    public function canReply(): bool { return !$this->isClosed(); }

    // Cek apakah tiket sudah melewati 7 hari tanpa respon
    public function shouldAutoLock(): bool
    {
        $lastActivity = $this->last_response_at ?? $this->created_at;
        return $this->status === 'open' || $this->status === 'diproses'
            ? $lastActivity->diffInDays(now()) >= 7
            : false;
    }

    public function sisaHariLock(): int
    {
        $lastActivity = $this->last_response_at ?? $this->created_at;
        $sisa = 7 - $lastActivity->diffInDays(now());
        return max(0, $sisa);
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    // Nama yang ditampilkan (anonim jika perlu)
    public function namaDisplay(bool $isAdminOrKepsek = false): string
    {
        if ($this->is_anonim && !$isAdminOrKepsek) {
            return '🎭 Anonim (' . ucfirst($this->role_pembuat) . ')';
        }
        return $this->user?->name ?? 'Unknown';
    }

    // Scope
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'diproses']);
    }
}
