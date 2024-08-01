<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Exception;

class BookingService
{
    public function checkRoomAvailabilityOnBetweenDates($roomTypeId, $startDate, $endDate)
    {
        $totalRooms = Room::where('room_type_id', $roomTypeId)->count();
        $bookedRooms = Booking::whereHas('room', function ($query) use ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        })->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })->count();

        return $totalRooms - $bookedRooms;
    }

    public function getAvailableRoomId($roomTypeId, $startDate, $endDate)
    {
        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        foreach ($rooms as $room) {
            // Check if the room itself is booked
            if ($this->isRoomBooked($room->id, $startDate, $endDate)) {
                continue;
            }

            // Check if the parent room is booked
            if ($room->parent_id && $this->isRoomBooked($room->parent_id, $startDate, $endDate)) {
                continue;
            }

            // Check if any child rooms are booked
            $childRooms = Room::where('parent_id', $room->id)->get();
            $isChildBooked = false;
            foreach ($childRooms as $childRoom) {
                if ($this->isRoomBooked($childRoom->id, $startDate, $endDate)) {
                    $isChildBooked = true;
                    break;
                }
            }

            if ($isChildBooked) {
                continue;
            }

            return $room->id;
        }

        throw new Exception('No available rooms found');
    }

    private function isRoomBooked($roomId, $startDate, $endDate)
    {
        return Booking::where('room_id', $roomId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->exists();
    }
}
