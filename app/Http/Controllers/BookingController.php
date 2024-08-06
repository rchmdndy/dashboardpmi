<?php
namespace App\Http\Controllers;

use App\Models\UserTransaction;
use App\Services\BookingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Str;
use Midtrans\Snap;
use NumberFormatter;
use Midtrans\Config;

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

    public function pay(){
        return view('bookings.pay');
    }

    public function bookRoom(Request $request) {

        $formatter = new NumberFormatter("id_ID", NumberFormatter::CURRENCY);

        // Validate request
        $userRequest = $request->validate([
            'user_email' => 'required|email',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|integer|min:1',
            'side' => 'string'
        ]);

        $availableRooms = $this->bookingService->checkRoomAvailabilityOnBetweenDates(
            $userRequest['room_type_id'],
            $userRequest['start_date'],
            $userRequest['end_date']
        );

        if ($availableRooms < $userRequest['amount']) {
            if ($userRequest['side'] == 'client') return response()->json('Room is not enough!', 409);
            return back()->withErrors(['amount' => 'Not enough rooms available for the selected dates']);
        }

        try {
            $roomType = RoomType::findOrFail($userRequest['room_type_id']);

            $days = $this->bookingService->getTotalDays($userRequest['start_date'], $userRequest['end_date']);

            $totalPrice = $roomType->price * $userRequest['amount'] * $days;

            $userTransaction = UserTransaction::create([
                'user_email' => $userRequest['user_email'],
                'order_id' => "PMI-BOOKING-".Str::uuid(),
                'transaction_date' => Carbon::now(),
                'amount' => $userRequest['amount'],
                'total_price' => $totalPrice,
                'transaction_status' => 'pending'
            ]);

            for ($i = 1; $i <= $userRequest['amount']; $i++) {
                Booking::create([
                    'user_transaction_id' => $userTransaction->id,
                    'user_email' => $userRequest['user_email'],
                    'room_id' => $this->bookingService->getAvailableRoomId($userRequest['room_type_id'], $userRequest['start_date'], $userRequest['end_date']),
                    'start_date' => $userRequest['start_date'],
                    'end_date' => $userRequest['end_date'],
                ]);
                $this->reportController->createReport($request);
            }

            Config::$serverKey = \config('midtrans.server_key');
            Config::$clientKey = \config('midrans.client_key');
            Config::$isProduction = false;
            Config::$isSanitized = false;
            Config::$is3ds = true;

            $params = array(
                'transaction_details' => array(
                    'order_id' => $userTransaction->order_id,
                    'gross_amount' => (int) $userTransaction->total_price,
                ),
                'customer_details' => array(
                    'name' => $userTransaction->user->name,
                    'email' => $userTransaction->user->email,
                    'phone' => $userTransaction->user->phone
                )
            );

            $snap_token = Snap::getSnapToken($params);
            $userTransaction->update([
               'snap_token' => $snap_token
            ]);

            if ($userRequest['side'] == 'client') return response()->json([
                'snap_token' => $snap_token,
                'client_key' => \config('midtrans.client_key')
            ], 200);
            return view('bookings.pay', ['snap_token' => $snap_token]);
        } catch (Exception $e) {
            if ($userRequest['side'] == 'client') return response([$e->getMessage()], 419);
            return back()->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()]);
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
