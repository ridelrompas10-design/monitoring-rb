<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Tambahkan casts ini agar data JSON (latihan tambahan) otomatis dikonversi jadi array
    protected $casts = [
        'extra_exercises' => 'array',
        'record_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}