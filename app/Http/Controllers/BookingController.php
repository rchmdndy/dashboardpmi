<?php
namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService) {
        $this->bookingService = $bookingService;
    }

    public function create() {
        $roomTypes = RoomType::all();
        $user = Auth::user();
        return view('bookings.create', compact('roomTypes', 'user'));
    }

    public function bookRoom(Request $request) {
        // Validate request
        $userRequest = $request->validate([
            'user_uuid' => 'required',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|integer|min:1'
        ]);

        // $user = Auth::user();
        // dd($user);

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

            Booking::create([
                'user_id' => $userRequest['user_uuid'],
                'room_id' => $this->bookingService->getAvailableRoomId($userRequest['room_type_id'], $userRequest['start_date'], $userRequest['end_date']),
                'start_date' => $userRequest['start_date'],
                'end_date' => $userRequest['end_date'],
                'total_price' => $totalPrice,
            ]);

            return redirect()->route('bookings.create')->with('success', 'Booking successful!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
        }
    }
}
