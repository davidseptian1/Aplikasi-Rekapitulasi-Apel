<?php

namespace App\Models;

use App\Models\ApelAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keterangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $table = 'keterangans';
    protected $primaryKey = 'id';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Definisikan relasi ke ApelAttendance.
     * Ini penting untuk pengecekan di KeteranganController@destroy.
     */
    public function apelAttendances()
    {
        return $this->hasMany(ApelAttendance::class, 'keterangan_id');
    }
}
