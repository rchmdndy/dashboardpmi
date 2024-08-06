<?php
// app/Models/UserTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperUserTransaction
 */
class UserTransaction extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'user_email',
        'order_id',
        'snap_token',
        'transaction_date',
        'amount',
        'total_price',
        'transaction_status',
    ];

    public function booking()
    {
        return $this->hasMany(Booking::class, 'user_transaction_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }
}
