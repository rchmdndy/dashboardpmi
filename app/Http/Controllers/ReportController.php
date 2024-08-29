<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NumberFormatter;

class ReportController extends Controller
{
    public function createReport(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $roomTypeId = (int) $request['room_type_id'];
        if (! $this->checkRoom($roomTypeId)) {
            Report::create([
                'room_type_id' => $roomTypeId,
                'total_bookings' => 1,
                'total_income' => RoomType::find($roomTypeId)->value('price'),
            ]);

            return response()->json(['New Report successfully created'], 201);
        } else {
            Report::where('room_type_id', '=', $roomTypeId)->whereMonth('created_at', $currentMonth)->update([
                'total_bookings' => DB::raw('total_bookings + 1'),
                'total_income' => DB::raw('total_income + '.RoomType::where('id', $roomTypeId)->value('price')),
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

    public function generateMonthly(Request $request)
    {
        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'Rp');

        $validated = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',

        ]);

        if($validated->fails()) return response()->json(["Data is not valid",$validated->failed()], 419);

        // Extract the numeric month value
        $month = (int) Carbon::createFromFormat('Y-m', $request->month)->format('m');
        $year = Carbon::createFromFormat('Y-m', $request->month)->format('Y');

        // Retrieve reports for the given month
        $reports = Report::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        // Format the reports
        $formattedReports = $reports->map(function ($report) use ($formatter) {
            return [
                'room_type' => $report->roomType ? $report->roomType->room_type : 'Unknown',
                'total_booking' => $report->total_bookings, // Assuming this field exists in your Report model
                'total_income' => $formatter->formatCurrency($report->total_income, 'IDR'),
            ];
        });

        // Return JSON response
        return response()->json($formattedReports);
    }
}
