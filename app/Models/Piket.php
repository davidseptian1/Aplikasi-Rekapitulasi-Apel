<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piket extends Model
{
    use HasFactory;

    protected $fillable = [
        'pajaga_by',
        'bajaga_first_by',
        'bajaga_second_by',
        'piket_date',
        'created_by'
    ];

    protected $casts = [
        'piket_date' => 'date',
    ];

    /**
     * Get the pajaga user
     */
    public function pajaga(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pajaga_by');
    }

    /**
     * Get the first bajaga user
     */
    public function bajagaFirst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bajaga_first_by');
    }

    /**
     * Get the second bajaga user
     */
    public function bajagaSecond(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bajaga_second_by');
    }

    /**
     * Get the creator user
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if piket exists for date
     */
    public static function existsForDate($date)
    {
        return self::whereDate('piket_date', $date)->exists();
    }
}
