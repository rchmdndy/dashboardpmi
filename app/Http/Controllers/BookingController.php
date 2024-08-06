<?php
namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

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
           'user_email' => 'required|email',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|integer|min:1'
        ]);

        // $user = Auth::user();
        $userRequest['user_email'] = $request->user_email;

        $availableRooms = $this->bookingService->checkRoomAvailabilityOnBetweenDates(
            $userRequest['room_type_id'],
            $userRequest['start_date'],
            $userRequest['end_date']
        );

        if ($availableRooms <= 0) {
            return back()->withErrors(['amount' => 'Not enough rooms available for the selected dates']);
        }

        try {   
            $roomType = RoomType::findOrFail($userRequest['room_type_id']);

            $days = $this->bookingService->getTotalDays($userRequest['start_date'], $userRequest['end_date']);

            $totalPrice = $roomType->price * $userRequest['amount'] * $days;

            Booking::create([
                'user_email' => $userRequest['user_email'],
                'room_id' => $this->bookingService->getAvailableRoomId($userRequest['room_type_id'], $userRequest['start_date'], $userRequest['end_date']),
                'start_date' => $userRequest['start_date'],
                'end_date' => $userRequest['end_date'],
                'total_price' => $totalPrice,
            ]);

            return response()->json(['message' => 'Room booked successfully'], 201);
        } catch (\Exception $e) {
            
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getBookings(Request $request)
    {
        $request->validate([
            'user_email' => 'required|email',
        ]);
    
        $userEmail = $request->user_email;
        
            $booking = Booking::where('user_email', $userEmail) 
            ->with('user') 
            ->with('room.roomType')
            ->get(); 

        if (!$booking) {
        return response()->json(['error' => 'No bookings found for this user'], 404);
        }

        foreach ($booking as $book) {
            $book['payment_status'] = "Pending";
        }

                return response()->json([
                    'booking' => $booking,
                ], 200);
            }


            public function show(Request $request)
            {
                $bookingId = $request->query('id'); // Use query parameter for ID

                $booking = Booking::where('id', $bookingId) 
                    ->with('user') 
                    ->with('room.roomType')
                    ->first(); 

                if (!$booking) {
                    return response()->json(['error' => 'Booking not found'], 404);
                }
                $booking['payment_status'] = "Completed"; // Manually add payment_status to the booking

                return response()->json([
                    'booking' => $booking,
                ]);
            }
            }


