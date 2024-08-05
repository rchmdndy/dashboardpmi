<?php
// app/Models/Report.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'total_bookings',
        'total_income',
        'created_at',
        'updated_at',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
