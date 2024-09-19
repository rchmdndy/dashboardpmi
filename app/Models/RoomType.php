<?php

// app/Models/RoomType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRoomType
 */
class RoomType extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'room_type',
        'capacity',
        'price',
        'description',
    ];

    public function room()
    {
        return $this->hasMany(Room::class, 'room_type_id', 'id');
    }

    public function room_image()
    {
        return $this->hasMany(RoomImage::class, 'room_type_id', 'id');
    }

    public function report()
    {
        return $this->hasMany(Report::class, 'room_type_id', 'id');
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }
}
