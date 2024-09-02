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
        // Fetch rooms of the specified room type
        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        // Initialize total room count and booked room count
        $totalRooms = $rooms->count();
        $bookedRooms = 0;

        foreach ($rooms as $room) {
            if (! $this->isRoomAvailable($room, $startDate, $endDate)) {
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

    public function isRoomAvailable($room, $startDate, $endDate)
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
            // dd('isBooked');
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
                // dd('parentBooked');
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
                // dd('childBooked');
                return false;
            }
        }

        // dd('berhasil');

        return true;
    }

    public function getTotalDays($startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $end = Carbon::createFromFormat('Y-m-d', $endDate);

        if ($start->greaterThan($end)) {
            return response()->json(['message' => 'End date must be greater than start date'], 400);
        }

        return abs($end->diffInDays($start));
    }

    public function getMeetingRoomForPackage($personCount, $startDate, $endDate)
    {
        $availableRoom = Room::join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->whereIn('room_types.id', [3, 4, 5, 6])
            ->where('room_types.capacity', '>=', $personCount)
            ->whereDoesntHave('booking', function ($query)  use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
            })
            ->first(['rooms.*']); // Make sure to select the columns you need

        return $availableRoom;
    }

    public function getLodgeRoomsForPackage($personCount, $startDate, $endDate)
    {
        $rooms = Room::join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->whereIn('rooms.room_type_id', [1, 2]) // Use whereIn for multiple room_type_ids
            ->whereDoesntHave('booking', function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
            })
            ->orderBy('room_types.capacity', 'desc') // Order by capacity from room_types table
            ->select('rooms.*', 'room_types.capacity') // Select the columns you need
            ->get();

        $totalCapacity = 0;
        $selectedRooms = [];

        foreach ($rooms as $room) {
            if ($totalCapacity >= $personCount) {
                break;
            }

            $roomCapacity = $room->capacity;

            $availableRooms = Room::where('id', $room->id)
                ->whereDoesntHave('booking', function ($query) use ($startDate, $endDate) {
                    $query->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                $query->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    });
                })
                ->count();

            if ($availableRooms) {
                $selectedRooms[] = $room->toArray();
                $totalCapacity += $roomCapacity;
            }
        }

        if ($totalCapacity < $personCount) {
            return false;
        }

        return $selectedRooms;
    }

    public function getAvailableRoomBooking($start_date, $end_date, $amount)
    {
        $roomData = Room::with(['roomType'])->whereDoesntHave('booking', function ($query) use ($start_date, $end_date) {
            $query->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date])
                    ->orWhere(function ($query) use ($start_date, $end_date) {
                        $query->where('start_date', '<=', $start_date)
                            ->where('end_date', '>=', $end_date);
                    });
            });
        })->get();

        $selectedRoomTypes = collect();
        $roomTypes = [];

        foreach ($roomData as $room) {
            $roomType = $room->roomType;
            $roomType->image = asset("storage/".$roomType->room_image->first()->image_path);
            unset($roomType->room_image);

            if (!isset($roomTypes[$roomType->id])) {
                $roomTypes[$roomType->id] = [
                    'type' => $roomType,
                    'capacity' => $roomType->capacity,
                ];
            } else {
                $roomTypes[$roomType->id]['capacity'] += $roomType->capacity;
            }
        }

        ksort($roomTypes);

        foreach ($roomTypes as $room_type_id => $room_type_data) {
            if ($room_type_data['capacity'] >= $amount) {
                $selectedRoomTypes->push($room_type_data['type']);
            }
        }

        return $selectedRoomTypes->values();
    }




}
