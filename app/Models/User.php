<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements JWTSubject , MustVerifyEmail
{
    use HasFactory, Notifiable, HasRelationships;
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

    public function user_transaction(){
        return $this->hasMany(UserTransaction::class);
    }

    public function booking(){
        return $this->hasMany(Booking::class);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }   
}
