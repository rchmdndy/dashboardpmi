<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NumberFormatter;
use function PHPUnit\Framework\isNull;

class UserTransactionController extends Controller
{

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function getUserTransaction(Request $request){
        $formatter = NumberFormatter::create('id_ID', NumberFormatter::CURRENCY);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'Rp');
        $userTransactions = UserTransaction::whereUserEmail($request->user_email)
                            ->get();
        $userTransactions = $userTransactions->map(function($userTransaction) use($formatter){
            if(!$userTransaction->booking->first()){
                $userTransaction->room_image = asset("storage/default_image.png");
                $userTransaction->room_type = null;
            }else{
                $userTransaction->room_image = asset("storage/".$userTransaction->booking->first()->room->roomType->room_image->first()->image_path);
                $userTransaction->room_type = $userTransaction->booking->first() ?$userTransaction->booking->first()->room->roomType->room_type: null;

            }
           unset($userTransaction->created_at);
           unset($userTransaction->updated_at);
           unset($userTransaction->booking);
           $userTransaction->total_price = $formatter->formatCurrency($userTransaction->total_price, 'IDR');
           return $userTransaction;
        })->reverse();
        if ($userTransactions->count() >= 1){
            return response()->json($userTransactions->toArray());
        }

        return response(['User email is not found in transaction table'], 409);
    }

    public function getUserTransactionByOrderID(Request $request)
    {
        $userTransactions = UserTransaction::where('id', $request->id)
            ->with('user')
            ->with('booking.room.roomType')
            ->first();

        if ($userTransactions) {
            return response()->json([
                'booking' => $userTransactions,
            ]);
        }

        return response(['OrderID is not found in transaction table'], 409);
    }

    public function refreshTransaction(Request $request)
    {
        $bookingId = $request->query('id'); // Use query parameter for ID

        $booking = UserTransaction::with('user')->find($bookingId);

        if (! $booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        return response()->json([
            'booking' => $booking,
        ]);
    }

    // app/Http/Controllers/TransactionController.php

    public function getSnapToken(Request $request)
    {
        $orderId = $request->query('id');
        $transaction = UserTransaction::where('id', $orderId)->firstOrFail();

        if ($transaction->snap_token) {
            return response()->json(['snap_token' => $transaction->snap_token]);
        } else {
            return response()->json(['error' => 'Snap token not found'], 404);
        }
    }

    public function detail(Request $request){
        $formatter = NumberFormatter::create('id_ID', NumberFormatter::CURRENCY);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'Rp');

        $transaction = UserTransaction::where('user_email', $request->user_email)->where('id', $request->id)->first();
        if (!$transaction) return response(['data is not available'], 419);
        if(!$transaction->bookings->first()){
            // return response(["fungsi masuk"], 200);
            $roomNames = null;
            $roomType = null;
            $roomImage = asset("storage/default_image.png");
            $roomDescription = null;
            $checkIn = null;
            $checkOut = null;
            $totalNight = null;
            $roomPerNightPrice = null;

        }else{
            $roomNames = $transaction->booking->map(function($booking){
                return $booking->room->room_name;
            });
            $roomType = $transaction->booking->first()->room->roomType->room_type;
            $roomImage = asset("storage/".$transaction->booking->first()->room->roomType->room_image->first()->image_path);
            $roomDescription = $transaction->booking->first()->room->roomType->description;
            $checkIn = $transaction->booking->first()->start_date;
            $checkOut = $transaction->booking->first()->end_date;
            $totalNight = $this->bookingService->getTotalDays( $transaction->booking->first()->start_date, $transaction->booking->first()->end_date);
            $roomPerNightPrice = $formatter->formatCurrency($transaction->booking->first()->room->roomType->price, "IDR");
        }

        $data = array(
            'guest_information' => [
                'user_name'  => $transaction->user->name,
                'room_booked' => $transaction->amount
            ],
            'room_detail' => [
                'room_name' => $roomNames,
                'room_type' => $roomType,
                'room_image' => $roomImage,
                'room_description' => $roomDescription
            ],
            "order_id" => $transaction->order_id,
            "check_in" => $checkIn,
            "check_out" =>  $checkOut,
            "total_night" => $totalNight,
            "payment_information" => [
                'payment_status' => $transaction->transaction_status,
                'channel' => $transaction->channel,
                'room_per_night_price' => $roomPerNightPrice,
                'total_price' => $formatter->formatCurrency($transaction->total_price, "IDR"),
                'snap_token' => $transaction->snap_token
            ]

        );
        return response()->json($data);
    }

}
