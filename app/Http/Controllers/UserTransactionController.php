<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;

class UserTransactionController extends Controller
{
    public function getUserTransaction(Request $request)
    {
        $userTransactions = UserTransaction::whereUserEmail($request->user_email)
            ->get();
        if ($userTransactions->count() >= 1) {
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
}
