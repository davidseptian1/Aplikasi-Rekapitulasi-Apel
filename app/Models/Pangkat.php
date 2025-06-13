<?php

namespace App\Models;

use App\Models\Biodata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    /**
     * Get all of the biodatas for the Pangkat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function biodatas()
    {
        // Sebuah Pangkat bisa dimiliki oleh banyak Biodata
        return $this->hasMany(Biodata::class);
    }
}
