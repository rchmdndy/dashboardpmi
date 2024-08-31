<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use NumberFormatter;
use App\Models\Report;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        // Validate the input month
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);
        //        return response('masuk function report');

        // Extract the numeric month value
        $month = (int) Carbon::createFromFormat('Y-m', $validated['month'])->format('m');
        $year = Carbon::createFromFormat('Y-m', $validated['month'])->format('Y');

        // Retrieve reports for the given month
        $reports = Report::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        // Format the reports
        $formattedReports = $reports->map(function ($report) use ($formatter) {
            $report->room_type_name = $report->roomType ? $report->roomType->room_type : 'Unknown'; // Ensure relationship exists
            $report->total_income = $formatter->formatCurrency($report->total_income, 'IDR');
            unset($report->roomType);
            unset($report->created_at);
            unset($report->updated_at);

            return $report;
        });

        // Return JSON response
        return response()->json($formattedReports);
    }

    public function printReport(Request $request){
        $email = $request->user;

        $user = User::whereEmail($email)->firstOrFail();
        $recordIds = $request->input('records', []);
        $records = Report::whereIn('id', $recordIds)->orderBy('created_at', 'desc')->get();
        // dd(request()->all(), $recordIds);
        $totalIncome = $records->sum('total_income');
    
        return view('reports.print', ['records' => $records, 'user' => $user, 'totalIncome' => $totalIncome]);
    }
}
