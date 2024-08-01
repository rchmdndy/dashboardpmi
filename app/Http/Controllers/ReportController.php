<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private Carbon $currentTime;

    public function __construct(Carbon $currentTime)
    {
        $this->currentTime = $currentTime::now();
    }


    public function createReport(Request $request){
        $roomTypeId = (int) $request['room_type_id'];
        if(!$this->checkRoom($roomTypeId)){
            Report::create([
                'room_type_id' => $roomTypeId,
                'total_bookings' => 1,
                'total_income' => RoomType::find($roomTypeId)->value('price'),
                'created_at' => $this->currentTime->month
            ]);
            return response()->json(['New Report successfully created'], 201);
        }else{
            Report::where('room_type_id', '=', $roomTypeId)->whereMonth('created_at', $this->currentTime->month)->update([
                'total_bookings' => DB::raw('total_bookings + 1'),
                'total_income' => DB::raw('total_income + ' . RoomType::where('id', $roomTypeId)->value('price'))
            ]);
            return response()->json(['Report successfully updated'], 201);
        }
    }

    public function checkRoom($roomTypeId){
        // Find the report where the room_type_id matches and the created_at month is the current month
        return Report::whereMonth('created_at','=',$this->currentTime->month)
                        ->where('room_type_id', $roomTypeId)
                        ->first();
    }
}
