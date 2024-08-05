<?php
namespace App\Http\Controllers;

use App\Services\BookingService;
use Exception;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReportController;

class BookingController extends Controller
{
    protected $bookingService;
    protected $reportController;

    public function __construct(BookingService $bookingService, ReportController $reportController) {
        $this->bookingService = $bookingService;
        $this->reportController = $reportController;
    }

    public function create() {
        $roomTypes = RoomType::all();
        $user = Auth::user();
        return view('bookings.create', compact('roomTypes', 'user'));
    }

    public function bookRoom(Request $request) {

        // Validate request
        $userRequest = $request->validate([
            'user_email' => 'required|email',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|integer|min:1'
        ]);

        $availableRooms = $this->bookingService->checkRoomAvailabilityOnBetweenDates(
            $userRequest['room_type_id'],
            $userRequest['start_date'],
            $userRequest['end_date']
        );

        if ($availableRooms <= 0) {
            return back()->withErrors(['amount' => 'Not enough rooms available for the selected dates']);
        }

        try {
            $totalPrice = RoomType::findOrFail($userRequest['room_type_id'])->price;

            for ($i = 1; $i <= $userRequest['amount']; $i++) {
                Booking::create([
                    'user_email' => $userRequest['user_email'],
                    'room_id' => $this->bookingService->getAvailableRoomId($userRequest['room_type_id'], $userRequest['start_date'], $userRequest['end_date']),
                    'start_date' => $userRequest['start_date'],
                    'end_date' => $userRequest['end_date'],
                    'total_price' => $totalPrice,
                ]);
                $this->reportController->createReport($request);
            }

            return redirect()->route('bookings.create')->with('success', 'Booking successful!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
        }
    }
}
