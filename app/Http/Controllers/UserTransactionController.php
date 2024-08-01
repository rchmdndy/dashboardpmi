<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;

class UserTransactionController extends Controller
{
    public function createTransactionRecord(Request $request){
        UserTransaction::create([
            'room_type_id' => ''
    ]);
    }
}
