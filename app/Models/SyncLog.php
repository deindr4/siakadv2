<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'user_id', 'jenis', 'semester_id', 'total_data',
        'created', 'updated', 'archived', 'failed', 'errors', 'status', 'durasi_detik',
    ];

    protected $casts = ['errors' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
}
