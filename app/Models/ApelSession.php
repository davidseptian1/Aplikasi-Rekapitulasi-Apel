<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subdis;
use App\Models\ApelAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApelSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'subdis_id',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the subdis for this session
     */
    public function subdis(): BelongsTo
    {
        return $this->belongsTo(Subdis::class, 'subdis_id');
    }


    /**
     * Get the creator of the apel session
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all attendances for this session
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(ApelAttendance::class);
    }

    /**
     * Check if session exists for date and type
     */
    public static function existsForDateAndType($date, $type, $subdisId = null)
    {
        $query = self::whereDate('date', $date)
            ->where('type', $type);

        if ($subdisId) {
            $query->where('subdis_id', $subdisId);
        }

        return $query->exists();
    }
}
