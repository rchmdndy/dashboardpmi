<?php
// app/Models/Room.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'room_name',
        'parent_id',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }

    public function booking(){
        return $this->hasMany(Booking::class);
    }
    public function parentRoom()
    {
        return $this->belongsTo(Room::class, 'parent_id', 'id');
    }
}
