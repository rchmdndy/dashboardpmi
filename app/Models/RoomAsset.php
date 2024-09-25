<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'inventory_id',
        'isBroken',
        'comment'
    ];

    public function room(){
        return $this->belongsTo(Room::class);
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }
}
