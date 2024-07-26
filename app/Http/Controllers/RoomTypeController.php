<?php

namespace App\Http\Controllers;

use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{

    // api stuff
    public function getAll(){
        return response()->json(RoomType::all()->map(function ($roomType){
            $roomType->image = asset('storage/'.RoomImage::select('image_path')->where('room_type_id', $roomType->id)->first()->image_path ?? 'default_image.jpg');
            return $roomType;
        })->all());
    }

    public function getDetail(Request $request){
        $room_type_id = $request->input('id');
        $room_type = RoomType::find($room_type_id);
        $room_images = RoomImage::select('image_path')->where('room_type_id', $room_type_id)->get();

        if ($room_images->isEmpty()){
            $imagePaths = [];
        }else{
            $imagePaths = $room_images->pluck('image_path')->map(function ($imagePath){
                return asset('storage/'.$imagePath);
            })->toArray();
        }

        return response()->json([
            'room_data' => $room_type,
            'images' => $imagePaths
        ]);
    }
}
