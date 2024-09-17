<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCustomer
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
      "name",
      "nik"
    ];

    public function booking_customer(){
        return $this->hasMany(BookingCustomer::class);
    }
}
