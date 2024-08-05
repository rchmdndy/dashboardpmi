<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserTransactionController extends Controller
{
    public function createTransaction(Request $request, array $bookingId){
        // TODO: Unfinsihed
        $q = $request;
        UserTransaction::create([
            'user_email' => $q['user_email'],
            'order_id' => Str::uuid(),
            'booking_id' => implode(",", $bookingId),
        ]);
    }
}
