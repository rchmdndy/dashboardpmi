<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperBookingCustomer
 */
class BookingCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'booking_id'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
}
