<?php
// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBooking
 */
class Booking extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'user_email',
        'user_transaction_id',
        'room_id',
        'start_date',
        'end_date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

    public function user_transaction(){
        return $this->belongsTo(UserTransaction::class, 'user_transaction_id', 'id');
    }
}
