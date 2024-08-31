<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPackage
 */
class Package extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'name',
        'price_per_person',
        'min_person_quantity',
        'hasLodgeRoom',
        'hasMeetingRoom',
        'description',
        'image',
    ];

    public function getImageAttribute($value)
    {
        return 'images/packages/'.$value;
    }

    // Mutator
    public function setImageAttribute($value)
    {
        $this->attributes['image'] = str_replace('images/packages/', '', $value);
    }
}
