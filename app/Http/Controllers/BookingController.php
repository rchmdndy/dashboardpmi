<?php

namespace App\Http\Controllers;

use App\Services\WhatsappNotificationService;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Midtrans\Snap;
use App\Models\User;
use Midtrans\Config;
use App\Models\Booking;
use App\Models\Package;
use App\Models\RoomType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserTransaction;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isNull;
use Illuminate\Support\Facades\Validator;


class BookingController extends Controller
{
    protected $bookingService;

    protected $whatsappService;

    protected $reportController;

    public function __construct(BookingService $bookingService, ReportController $reportController, WhatsappNotificationService $whatsappService)
    {
        $this->bookingService = $bookingService;
        $this->reportController = $reportController;
        $this->whatsappService = $whatsappService;
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function create()
    {
        $roomTypes = RoomType::all();
        $user = Auth::user();

        return view('bookings.create', compact('roomTypes', 'user'));
    }

    public function pay()
    {
        return view('bookings.pay');
    }

    public function bookRoom(Request $request) {
        $formatter = \NumberFormatter::create('id_ID', \NumberFormatter::CURRENCY);
        $formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, 'Rp');


        // Validate request
        $userRequest = $request->all();

        $validator = Validator::make($userRequest, [
            'user_email' => 'required|email|exists:users,email',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'start_date' => 'required|date|after_or_equal:today|date_format:Y-m-d',
            'end_date' => 'required|date|after:start_date|date_format:Y-m-d',
            'amount' => 'required|integer|min:1',
            'side' => 'string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('user_email')) {
                return response()->json(['error' => 'Email tidak diisi atau tidak valid'], 400);
            } elseif ($errors->has('room_type_id')) {
                return response()->json(['error' => 'Tipe kamar tidak valid'], 400);
            } elseif ($errors->has('start_date')) {
                return response()->json(['error' => 'Tanggal check in minimal adalah hari ini'], 400);
            } elseif ($errors->has('end_date')) {
                return response()->json(['error' => 'Tanggal check out harus setelah tanggal check in'], 400);
            } elseif ($errors->has('amount')) {
                return response()->json(['error' => 'Jumlah kamar minimal adalah 1'], 400);
            } else {
                return response()->json(['error' => 'Invalid request'], 400);
            }
        }

        $availableRooms = $this->bookingService->checkRoomAvailabilityOnBetweenDates(
            $userRequest['room_type_id'],
            $userRequest['start_date'],
            $userRequest['end_date']
        );

        if ($availableRooms < $userRequest['amount']) {
            if ($userRequest['side'] == 'client') {
                return response()->json(['error' => 'Ruangan tipe ini tidak tersedia untuk tanggal yang dipilih, Coba Yang lain!'], 409);
            }

            return back()->withErrors(['amount' => 'Not enough rooms available for the selected dates']);
        }

        try {
            $roomType = RoomType::findOrFail($userRequest['room_type_id']);

            $days = $this->bookingService->getTotalDays($userRequest['start_date'], $userRequest['end_date']);

            $totalPrice = $roomType->price * $userRequest['amount'] * $days;

            $userTransaction = UserTransaction::create([
                'user_email' => $userRequest['user_email'],
                'channel' => 'direct',
                'order_id' => 'PMI-BOOKING-'.Str::uuid(),
                'transaction_date' => now(),
                'amount' => $userRequest['amount'],
                'total_price' => $totalPrice,
                'transaction_status' => 'pending',
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

            $booking_detail = array(
                "guest_information" => [
                    "user_name" => $userTransaction->user->name,
                    "room_booked" => $userTransaction->amount
                ],
                "room_detail" => [
                    "room_name" => $userTransaction->booking->first()->room->roomType->room_type,
                    "room_image" => asset('storage/images/kamar/'.$userTransaction->booking->first()->room->roomType->room_image->first()->image_path)
                ],
                "order_id" => $userTransaction->order_id,
                "check_in" => $userRequest['start_date'],
                "check_out" => $userRequest['end_date'],
                "total_night" => $this->bookingService->getTotalDays($userRequest['start_date'], $userRequest['end_date']),
                "payment_information" => [
                    'room_per_night_price' => $formatter->formatCurrency($userTransaction->booking->first()->room->roomType->price, "IDR"),
                    'total_price' => $formatter->formatCurrency($userTransaction->total_price, "IDR"),
                ]
            );

            $params = [
                'transaction_details' => [
                    'order_id' => $userTransaction->order_id,
                    'gross_amount' => (int) $userTransaction->total_price,
                ],
                'customer_details' => [
                    'name' => $userTransaction->user->name,
                    'email' => $userTransaction->user->email,
                    'phone' => $userTransaction->user->phone,
                ],
            ];

            $snap_token = Snap::getSnapToken($params);
            $userTransaction->update([
                'snap_token' => $snap_token,
            ]);


            $data = [
                'order_id' => $userTransaction->order_id,
                'name' => $userTransaction->user->name,
                'start_date' => $userRequest['start_date'],
                'end_date' => $userRequest['end_date'],
                'room_type' => $userTransaction->booking->first()->room->roomType->room_type,
                'rooms' => $userTransaction->booking->map(function($booking){
                    return $booking->room->room_name;
                }),
                'total_price' => $formatter->formatCurrency($userTransaction->total_price, "IDR")
            ];

            $transactionLink = URL::to("https://palmerinjateng.id/?id=$userTransaction->id&user_email=$userTransaction->user_email");

            $this->whatsappService->sendMessage(
                $userTransaction->user->phone ?? null,
                $transactionLink,
                $data
            );

            if ($userRequest['side'] == 'client') return response()->json([
                'booking_detail' => $booking_detail,
                'snap_token' => $snap_token,
                'client_key' => \config('midtrans.client_key')

            ], 200);


            return view('bookings.pay', ['snap_token' => $snap_token]);
        } catch (Exception $e) {
            if ($userRequest['side'] == 'client') {
                return response([$e->getMessage()], 419);
            }

            return back()->withErrors(['error' => 'Failed to create booking: '.$e->getMessage()]);
        }
    }

    public function bookPackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email',
            'package_id' => 'required|exists:packages,id',
            'person_count' => 'required|integer|min:20|max:82',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('person_count')) {
                return response()->json(['error' => 'Jumlah minimal orang dalam paket ini adalah 20'], 400);
            } elseif ($errors->has('start_date')) {
                return response()->json(['error' => 'Tanggal check in minimal adalah hari ini'], 400);
            } elseif ($errors->has('end_date')) {
                return response()->json(['error' => 'Tanggal check out harus setelah tanggal check in'], 400);
            } elseif ($errors->has('package_id')) {
                return response()->json(['error' => 'Paket tidak terdaftar'], 400);
            } elseif ($errors->has('user_email')) {
                return response()->json(['error' => 'Email tidak diisi atau tidak valid'], 400);
            }

            return response()->json(['error' => 'Invalid request'], 400);
        }

        $package = Package::findOrFail($request->package_id);
        $personCount = $request->person_count;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $totalDays = $this->bookingService->getTotalDays($request->start_date, $request->end_date);

        if ($personCount < $package->min_person_quantity) {
            return response()->json(['error' => 'Minimum person count not met.'], 400);
        }

        if ($package->hasMeetingRoom == 1 && $package->hasLodgeRoom == 0) {
            $meetingRoom = $this->bookingService->getMeetingRoomForPackage($personCount, $startDate, $endDate);

            if (! $meetingRoom) {
                return response()->json(['error' => 'No available meeting room found.'], 400);
            }

            $total_price = $package->price_per_person * $request->person_count * $totalDays;
            $userTransaction = UserTransaction::create([
                'user_email' => $request->user_email,
                'channel' => 'packages',
                'order_id' => 'PMI-BOOKING-'.Str::uuid(),
                'transaction_date' => now(),
                'amount' => $personCount,
                'total_price' => $total_price,
                'transaction_status' => 'pending',
            ]);

            // Book the meeting room
            Booking::create([
                'user_email' => $request->user_email,
                'user_transaction_id' => $userTransaction->id,
                'room_id' => $meetingRoom->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        } elseif ($package->hasMeetingRoom == 1 && $package->hasLodgeRoom == 1) {
            $meetingRoom = $this->bookingService->getMeetingRoomForPackage($personCount, $startDate, $endDate);
            $lodgeRooms = $this->bookingService->getLodgeRoomsForPackage($personCount, $startDate, $endDate);

            if (! $meetingRoom) {
                return response()->json(['error' => 'No available meeting room found.'], 400);
            }

            if (empty($lodgeRooms)) {
                return response()->json(['error' => 'No available lodge rooms found.'], 400);
            }


            $total_price = $package->price_per_person * $request->person_count * $totalDays;
            $userTransaction = UserTransaction::create([
                'user_email' => $request->user_email,
                'channel' => 'packages',
                'order_id' => 'PMI-BOOKING-'.Str::uuid(),
                'transaction_date' => now(),
                'amount' => $personCount,
                'total_price' => $total_price,
                'transaction_status' => 'pending',
            ]);

            // Book the meeting room
            Booking::create([
                'user_email' => $request->user_email,
                'user_transaction_id' => $userTransaction->id,
                'room_id' => $meetingRoom->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Book lodge rooms
            foreach ($lodgeRooms as $room) {
                Booking::create([
                    'user_email' => $request->user_email,
                    'user_transaction_id' => $userTransaction->id,
                    'room_id' => $room['id'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }

        $params = [
            'transaction_details' => [
                'order_id' => $userTransaction->order_id,
                'gross_amount' => (int) $userTransaction->total_price,
            ],
            'customer_details' => [
                'name' => $userTransaction->user->name,
                'email' => $userTransaction->user->email,
                'phone' => $userTransaction->user->phone,
            ],
        ];

        $snap_token = Snap::getSnapToken($params);
        $userTransaction->update([
            'snap_token' => $snap_token,
        ]);

        return response()->json([
            'snap_token' => $snap_token,
            'client_key' => \config('midtrans.client_key'),
        ]);
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

        if (! $booking) {
            return response()->json(['error' => 'No bookings found for this user'], 404);
        }

        foreach ($booking as $book) {
            $book['payment_status'] = 'Pending';
        }

                return response()->json([
                    'booking' => $booking,
                ], 200);
            }

    public function getAvailableRoomOnDate(Request $request){
        $validate = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|integer|min:1',
        ]);

        if($validate->fails()) return response()->json(["Data is not valid",$validate->failed()], 419);

        $availableRoomData = $this->bookingService->getAvailableRoomBooking($request->start_date, $request->end_date, $request->amount);

        return (!isNull($availableRoomData) ? response()->json('All room is fully booked') : response()->json($availableRoomData));
    }


    public function printBooking(Request $request){
        $email = $request->user;

        $user = User::whereEmail($email)->firstOrFail();
        $recordIds = $request->input('records', []);
        $records = Booking::whereIn('id', $recordIds)->orderBy('created_at', 'asc')->get();
        $records->map(function($record){
            $record->user_transaction->transaction_date = Carbon::parse($record->user_transaction->transaction_date);
        });
        // dd(request()->all(), $recordIds);

        return view('bookings.print', ['records' => $records, 'user' => $user]);
    }
}
