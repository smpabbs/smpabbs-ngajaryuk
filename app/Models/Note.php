<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'class',
        'subject',
        'teacher_id',
        'date',
        'time',
        'note',
        'checked'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
