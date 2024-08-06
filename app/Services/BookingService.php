<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Exception;

class BookingService
{
    public function checkRoomAvailabilityOnBetweenDates($roomTypeId, $startDate, $endDate)
    {
        // Fetch rooms of the specified room type
        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        // Initialize total room count and booked room count
        $totalRooms = $rooms->count();
        $bookedRooms = 0;

        foreach ($rooms as $room) {
            if (!$this->isRoomAvailable($room, $startDate, $endDate)) {
                $bookedRooms++;
            }
        }

        return $totalRooms - $bookedRooms;
    }

    /**
     * @throws Exception
     */
    public function getAvailableRoomId($roomTypeId, $startDate, $endDate)
    {
        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        foreach ($rooms as $room) {
            if ($this->isRoomAvailable($room, $startDate, $endDate)) {
                return $room->id;
            }
        }

        throw new Exception('No available rooms found');
    }

    private function isRoomAvailable($room, $startDate, $endDate)
    {
        // Check if the room itself is booked
        $isBooked = Booking::where('room_id', $room->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->exists();

        if ($isBooked) {
            return false;
        }

        // Check if the parent room is booked
        if ($room->parent_id) {
            $parentBooked = Booking::where('room_id', $room->parent_id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })->exists();

            if ($parentBooked) {
                return false;
            }
        }

        // Check if any child room is booked
        $childRooms = Room::where('parent_id', $room->id)->get();
        foreach ($childRooms as $childRoom) {
            $childBooked = Booking::where('room_id', $childRoom->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })->exists();

            if ($childBooked) {
                return false;
            }
        }

        return true;
    }
}
