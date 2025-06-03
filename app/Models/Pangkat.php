<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nilai_pangkat', // Tambahkan ini
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pangkats';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'nilai_pangkat' => 'integer', // Tambahkan cast jika perlu
    ];
}
