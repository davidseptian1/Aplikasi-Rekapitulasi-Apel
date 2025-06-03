<?php

namespace App\Models;

use App\Models\User;
use App\Models\Keterangan;
use App\Models\ApelSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApelAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'apel_session_id',
        'user_id',
        'keterangan_id',
        'status',
        'notes',
        'submitted_by',
        'submitted_at',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the apel session
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ApelSession::class, 'apel_session_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the keterangan
     */
    public function keterangan(): BelongsTo
    {
        return $this->belongsTo(Keterangan::class);
    }

    /**
     * Get the submitter
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the verifier
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope for draft status
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for submitted status
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope for verified status
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }
}
