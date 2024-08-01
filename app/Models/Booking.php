<?php
// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;
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
        'room_id',
        'start_date',
        'end_date',
        'amount',
        'total_price',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_transaction(){
        return $this->hasOne(UserTransaction::class);
    }
}
