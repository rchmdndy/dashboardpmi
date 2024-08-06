<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserTransactionController extends Controller
{
    public function getUserTransaction(Request $request){
        $userTransactions = UserTransaction::whereUserEmail($request->user_email)->get();
        if ($userTransactions->count() >= 1){
            return response()->json($userTransactions->toArray());
        }
        return response(['User email is not found in transaction table'], 409);
    }
}
