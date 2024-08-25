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
        'image'
    ];

    public function package_images(){
        return $this->hasMany(PackageImages::class, 'package_id', 'id');
    }
}
