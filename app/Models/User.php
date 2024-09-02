<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser, JWTSubject, MustVerifyEmail
{
    use HasFactory, HasRelationships, Notifiable;

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'phone',
        // 'refresh_token',
        'role_id',
        'password',
        'email_verified_at',

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

    protected $visible = [
        'email',
        'name',
        'phone',
        'role_id',
        'email_verified_at',
    ];

    /**
     * Get the attributes that should be cast.
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

    public function canAccessPanel(Panel $panel): bool
    {
        // dd($panel);
        if ($panel->getId() === 'admin') {
            return $this->isAdmin() && $this->hasVerifiedEmail();
        } elseif ($panel->getId() === 'staff') {
            return $this->isStaff() && $this->hasVerifiedEmail();
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isStaff(): bool
    {
        return $this->role_id === 3;
    }

    public function isCustomer(): bool
    {
        return $this->role_id === 4;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function user_transaction()
    {
        return $this->hasMany(UserTransaction::class);
    }

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
