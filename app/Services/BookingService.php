<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Exception;

class BookingService
{
    public function checkRoomAvailabilityOnBetweenDates($roomTypeId, $startDate, $endDate)
    {
        // Check for parent and child room availability
        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        foreach ($rooms as $room) {
            if (!$this->isRoomAvailable($room, $startDate, $endDate)) {
                return 0; // No available rooms
            }
        }

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
        // Check if the room itself is available
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

        // Check parent room availability
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

        // Check child rooms availability
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

    public function getTotalDays($startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $end = Carbon::createFromFormat('Y-m-d', $endDate);

        if ($start->greaterThan($end)) {
            throw new Exception('Start date must be before end date.');
        }

        return abs($end->diffInDays($start));
    }


}
