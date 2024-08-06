<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function handleMidtransNotification(Request $request){
        $server_key = config('midtrans.server_key');
        $request = $request->toArray();
        $hashed = hash('sha512', $request['order_id'].$request['status_code'].$request['gross_amount'].$server_key);
        if ($hashed == $request['signature_key']){
            if (($request['transaction_status'] == 'capture' || 'settlement') && ($request['status_code'] == "200") && ($request['fraud_status'] == "accept")) {
                UserTransaction::whereOrderId($request['order_id'])->update(['transaction_status' => 'success']);
                return response('Notification Received', 200);
            }
            else if ($request['transaction_status'] == 'deny' || 'cancel' || 'expire' || 'failure'){
                UserTransaction::whereOrderId($request['order_id'])->update(['transaction_status' => 'failed']);
                return response('Notification Received', 200);
            }
        }else{
            return response("Hash not correct", 402);
        }
        return response('Update failed', 402);
    }
}
