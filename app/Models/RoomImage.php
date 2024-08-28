<?php

// app/Models/RoomImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRoomImage
 */
class RoomImage extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'room_type_id',
        'image_path',
    ];

    public $timestamps = false;

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }

    public function getImagePathAttribute($value)
    {
        return 'storage/images/kamar/'.$value;
    }

    // Mutator
    public function setImagePathAttribute($value)
    {
        $this->attributes['image_path'] = str_replace('images/kamar/', '', $value);
    }
}
