<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [];
    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function physicalRecords()
    {
        return $this->hasMany(PhysicalRecord::class);
    }
}