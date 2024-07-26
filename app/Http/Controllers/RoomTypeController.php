<?php

namespace App\Http\Controllers;

use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{
    public function getAll(){
        return response()->json($roomTypes = RoomType::all()->map(function ($roomType){
            $roomType->image = asset('storage/'.RoomImage::select('image_path')->where('room_type_id', $roomType->id)->first()->image_path ?? 'default_image.jpg');
            return $roomType;
        })->all());

    }
}
