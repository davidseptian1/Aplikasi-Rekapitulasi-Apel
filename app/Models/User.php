<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subdis;
use App\Models\Biodata;
use App\Models\Jabatan;
use App\Models\Pangkat;
use Illuminate\Support\Str;
use App\Models\ApelAttendance;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'no_telpon',
        'nrp',
        'username',
        'email',
        'password',
        'role',
        'photos',
        'is_active',
        'subdis_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Generate username if not provided
            if (empty($user->username)) {
                $user->username = Str::slug($user->name);

                // Ensure username is unique
                $count = User::where('username', $user->username)->count();
                if ($count > 0) {
                    $user->username = $user->username . '-' . ($count + 1);
                }
            }
        });
    }

    /**
     * Get the possible roles for the user.
     *
     * @return array
     */
    public static function getRoles(): array
    {
        return ['superadmin', 'pokmin', 'piket', 'pimpinan', 'personil'];
    }

    /**
     * Get the roles that can be assigned by other users.
     * Excludes 'superadmin'.
     *
     * @return array
     */
    public static function getAssignableRoles(): array
    {
        // Filter array untuk menghapus 'superadmin'
        return array_filter(self::getRoles(), function ($role) {
            return $role !== 'superadmin';
        });
    }

    /**
     * Get the biodata associated with the user.
     */
    public function biodata(): HasOne
    {
        return $this->hasOne(Biodata::class);
    }

    /**
     * Get the subdi that owns the user.
     */
    public function subdis(): BelongsTo
    {
        return $this->belongsTo(Subdis::class, 'subdis_id');
    }

    /**
     * Get the pangkat through biodata.
     */
    public function pangkat()
    {
        return $this->hasOneThrough(
            Pangkat::class,
            Biodata::class,
            'user_id',
            'id',
            'id',
            'pangkat_id'
        );
    }

    /**
     * Get the jabatan through biodata.
     */
    public function jabatan()
    {
        return $this->hasOneThrough(
            Jabatan::class,
            Biodata::class,
            'user_id',
            'id',
            'id',
            'jabatan_id'
        );
    }

    /**
     * Get the apel attendances for the user.
     */
    public function apelAttendances(): HasMany
    {
        return $this->hasMany(ApelAttendance::class, 'user_id');
    }

    /**
     * Get the photo URL attribute.
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photos
            ? asset('storage/uploads/photos/' . $this->photos)
            : asset('assets/img/default-user.jpg');
    }
}
