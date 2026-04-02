<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'unit',
        'lokasi',
        'alamat',
        'foto',
        'waktu',
    ];

    public $timestamps = true;
    
    protected $casts = [
        'waktu' => 'datetime',
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
