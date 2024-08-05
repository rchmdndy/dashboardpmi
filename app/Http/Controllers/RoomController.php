<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{

    public function getAll(){
        $rooms = Room::with(['roomType:id,room_type', 'parentRoom:id,room_name'])
                ->select('id','room_type_id', 'room_name', 'parent_id')
                ->get()
                ->map(function ($room){
                    return [
                        'id' => $room->id,
                        'room_type' => $room->roomType->room_type,
                        'room_name' => $room->room_name,
                        'parent_room' => $room->parentRoom->room_name ?? null,
                    ];
                });
        return response()->json($rooms);
    }

    public function getDetail(Request $request){
        $room = Room::with(['roomType:id,room_type', 'parentRoom:id,room_name'])
                ->select('id', 'room_type_id', 'room_name', 'parent_id')
                ->findOrFail($request->input('id'));
        return response()->json([
            'id' => $room->id,
            'room_type' => $room->roomType->room_type,
            'room_name' => $room->room_name,
            'parent_room' => $room->parentRoom->room_name ?? null,
        ]);
    }

    public function getByRoomType(Request $request)
    {
        try {
            $room_type_id = $request->input('id');
            $rooms = Room::with(['roomType:id,room_type', 'parentRoom:id,room_name'])
                ->select('id', 'room_type_id', 'room_name', 'parent_id')
                ->where('room_type_id', '=', $room_type_id)
                ->get()
                ->map(function ($room) {
                    return [
                        'id' => $room->id,
                        'room_type' => $room->roomType->room_type,
                        'room_name' => $room->room_name,
                        'parent_room' => $room->parentRoom->room_name ?? null,
                    ];
                });

            if ($rooms->isEmpty()) {
                return response()->json(['error' => 'Rooms not found'], 404);
            }

            return response()->json($rooms);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    //CUD controller
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'room_type_id' => 'required|integer|exists:room_types,id',
            'room_name' => 'required|string|max:255',
        ]);

        try {
            $room = Room::create($validated);
            return response()->json($room, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create room', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing room
    public function update(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'room_type_id' => 'required|integer|exists:room_types,id',
            'room_name' => 'required|string|max:255',
        ]);

        try {
            $room = Room::findOrFail($id);
            $room->update($validated);
            return response()->json($room);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update room', 'message' => $e->getMessage()], 500);
        }
    }


    // Delete a room
    public function delete($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();
            return response()->json(['message' => 'Room deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete room', 'message' => $e->getMessage()], 500);
        }
    }

}
