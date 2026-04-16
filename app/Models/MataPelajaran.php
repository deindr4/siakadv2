<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode', 'nama', 'kelompok', 'tingkat', 'jam_per_minggu', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jurnalMengajar()
    {
        return $this->hasMany(JurnalMengajar::class);
    }
}
