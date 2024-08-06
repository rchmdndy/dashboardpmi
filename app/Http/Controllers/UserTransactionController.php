<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;

class UserTransactionController extends Controller
{
    public function getTransaction(Request $request){
        $request = $request->toArray();
        return response()->json(UserTransaction::whereUserEmail($request['user_email'])->get());
    }
}
