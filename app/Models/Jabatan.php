<?php

namespace App\Models;

use App\Models\Biodata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $table = 'jabatans';
    protected $primaryKey = 'id';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Definisikan relasi ke Biodata jika Jabatan digunakan di sana.
     * Ini berguna untuk pengecekan di controller sebelum menghapus.
     */
    public function biodatas()
    {
        return $this->hasMany(Biodata::class);
    }
}
