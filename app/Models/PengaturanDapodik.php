<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanDapodik extends Model
{
    protected $fillable = [
        'ip_address',
        'port',
        'npsn',
        'bearer_token',
        'is_active',
        'last_sync',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync' => 'datetime',
    ];

    // Ambil pengaturan aktif (singleton)
    public static function aktif(): ?self
    {
        return self::where('is_active', true)->latest()->first();
    }

    // Generate base URL
    public function getBaseUrlAttribute(): string
    {
        return 'http://' . $this->ip_address . ':' . $this->port;
    }
}
