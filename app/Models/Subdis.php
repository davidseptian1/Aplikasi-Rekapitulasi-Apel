<?php

namespace App\Models;

use App\Models\User;
use App\Models\ApelSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subdis extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id'
    ];

    protected $table = 'subdis';
    protected $primaryKey = 'id';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User (Penanggung Jawab).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Users (Personil yang ada di bawah Subdis ini).
     * Ini dibutuhkan untuk pengecekan di SubdisController@destroy.
     */
    public function users(): HasMany
    {
        // Asumsi di tabel 'users' ada kolom 'subdis_id'
        return $this->hasMany(User::class, 'subdis_id');
    }

    /**
     * Relasi ke ApelSession.
     * Ini dibutuhkan untuk pengecekan di SubdisController@destroy.
     */
    public function apelSessions(): HasMany
    {
        return $this->hasMany(ApelSession::class, 'subdis_id');
    }
}
