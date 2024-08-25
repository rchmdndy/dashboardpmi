<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageImages extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'package_id',
        'image_path'
    ];

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
