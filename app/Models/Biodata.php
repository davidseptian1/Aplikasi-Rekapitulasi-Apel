<?php

namespace App\Models;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Pangkat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Biodata extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pangkat_id',
        'jabatan_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'biodatas';
    protected $primaryKey = 'id';

    /**
     * Get the user that owns the biodata.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pangkat associated with the biodata.
     */
    public function pangkat(): BelongsTo
    {
        return $this->belongsTo(Pangkat::class);
    }

    /**
     * Get the jabatan associated with the biodata.
     */
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
