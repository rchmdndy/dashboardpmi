<?php
// app/Models/RoomImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRoomImage
 */
class RoomImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'image_path',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
