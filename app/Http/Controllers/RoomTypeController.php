<?php

namespace App\Http\Controllers;

use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{

    // api stuff
    /**
     * Mengembalikan semua row dari tabel room_type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(){
        return response()->json(RoomType::select('id', 'room_type', 'capacity', 'price', 'description')->get()->map(function ($roomType){
            $roomType->image = asset('storage/room_images/'.RoomImage::select('image_path')->where('room_type_id', $roomType->id)->first()->image_path ?? 'default_image.jpg');

            return $roomType;
        })->all());
    }

    /**
     * Mengembalikan row dari tabel room_type berdasarkan dengan id yang diberikan
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetail(Request $request){
        $room_type_id = $request->input('id');
        $room_type = RoomType::find($room_type_id);
        $room_images = RoomImage::select('image_path')->where('room_type_id', $room_type_id)->get();

        if ($room_images->isEmpty()){
            $imagePaths = [];
        }else{
            $imagePaths = $room_images->pluck('image_path')->map(function ($imagePath){
                return asset('storage/room_images/'.$imagePath);
            })->toArray();
        }

        return response()->json([
            'room_data' => $room_type,
            'room_images' => $imagePaths
        ]);
    }

    // Create a new room type
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $roomType = RoomType::create($validated);
            return response()->json($roomType, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create room type', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing room type
    public function update(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $roomType = RoomType::findOrFail($id);
            $roomType->update($validated);
            return response()->json($roomType);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update room type', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a room type
    public function delete($id)
    {
        try {
            $roomType = RoomType::findOrFail($id);
            $roomType->delete();
            return response()->json(['message' => 'Room type deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete room type', 'message' => $e->getMessage()], 500);
        }
    }


}
