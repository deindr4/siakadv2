<?php
// ============================================================
// app/Models/TiketRespon.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiketRespon extends Model
{
    use SoftDeletes;

    protected $table    = 'tiket_respon';
    protected $fillable = [
        'tiket_id', 'user_id', 'role_responder',
        'is_anonim', 'isi', 'foto', 'foto_original',
    ];

    protected $casts = ['is_anonim' => 'boolean'];

    public function tiket() { return $this->belongsTo(Tiket::class); }
    public function user()  { return $this->belongsTo(User::class); }

    public function namaDisplay(bool $isAdminOrKepsek = false): string
    {
        if ($this->is_anonim && !$isAdminOrKepsek) {
            return '🎭 Anonim (' . ucfirst($this->role_responder) . ')';
        }
        return $this->user?->name ?? 'Unknown';
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function isFromAdmin(): bool
    {
        return in_array($this->role_responder, ['admin', 'kepala_sekolah']);
    }
}
