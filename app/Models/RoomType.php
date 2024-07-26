<?php
// app/Models/RoomType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type',
        'capacity',
        'price',
        'description',
    ];

    public function room(){
        return $this->hasMany(Room::class);
    }

    public function room_image(){
        return $this->hasMany(RoomImage::class);
    }

    public function report(){
        return $this->hasMany(Report::class);
    }

}
