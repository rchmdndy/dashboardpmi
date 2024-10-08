<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Exception;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use App\Models\RoomType;


class ReportService{
    public function createReport(int $id)
    {
        $currentMonth = Carbon::now()->month;
        if (! $this->checkRoom($id)) {
            Report::create([
                'room_type_id' => $id,
                'total_bookings' => 1,
                'total_income' => RoomType::find($id)->value('price'),
            ]);

            return response()->json(['New Report successfully created'], 201);
        } else {
            Report::where('room_type_id', '=', $id)->whereMonth('created_at', $currentMonth)->update([
                'total_bookings' => DB::raw('total_bookings + 1'),
                'total_income' => DB::raw('total_income + '.RoomType::where('id', $id)->value('price')),
            ]);

            return response()->json(['Report successfully updated'], 201);
        }
    }

    public function checkRoom($roomTypeId)
    {
        $currentMonth = Carbon::now()->month;

        // Find the report where the room_type_id matches and the created_at month is the current month
        return Report::whereMonth('created_at', '=', $currentMonth)
            ->where('room_type_id', $roomTypeId)
            ->first();
    }
}
