<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function room_assets(){
        return $this->hasMany(RoomAsset::class);
    }

}
