<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Exception;


class BookingService
{
    public function checkRoomAvailabilityOnBetweenDates($roomTypeId, $startDate, $endDate){
        $totalRooms = Room::where('room_type_id', $roomTypeId)->count();
        $bookedRooms = Booking::whereHas('room', function ($query) use($roomTypeId){
           $query->where('room_type_id', $roomTypeId) ;
        })->where(function ($query) use ($startDate, $endDate){
            $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($query) use ($startDate, $endDate){
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
        })->count();

        return $totalRooms - $bookedRooms;
    }

    public function getAvailableRoomId($roomTypeId, $startDate, $endDate){
        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        foreach ($rooms as $room) {
            $isBooked = Booking::where('room_id', $room->id)
                ->where(function($query) use ($startDate, $endDate){
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function($query) use ($startDate, $endDate){
                            $query->where('startd_date', '<=', '$startDate')
                                ->where('end_date', '>=', $endDate);
                        });
                })->exists();
                if(!$isBooked){
                    return $room->id;
                }
        }
        throw new Exception('No available rooms found');
    }

}
