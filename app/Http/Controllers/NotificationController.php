<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class NotificationController extends Controller
{
    // public function handleMidtransNotification(Request $request){
    //     $server_key = config('midtrans.server_key');
    //     $request = $request->toArray();
    //     $hashed = hash('sha512', $request['order_id'].$request['status_code'].$request['gross_amount'].$server_key);
    //     if ($hashed == $request['signature_key']){
    //         if ($request['transaction_status'] == 'capture') {
    //             UserTransaction::whereOrderId($request['order_id'])->update(['transaction_status' => 'success']);
    //             return response('Notification Received', 200);
    //         }
    //     }else{
    //         return response("Hash not correct", 402);
    //     }
    //     return response('Update failed', 402);
    // }

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function handleMidtransNotification(Request $request)
    {
        Log::info('Notification Handler Triggered');
        Log::info('Midtrans Configuration:', [
            'server_key' => config('midtrans.server_key'),
            'client_key' => config('midtrans.client_key'),
            'is_production' => config('midtrans.is_production'),
            'is_sanitized' => config('midtrans.is_sanitized'),
            'is_3ds' => config('midtrans.is_3ds'),
        ]);

        try {
            //incoming JSON payload dari midtrans
            $notif = new \Midtrans\Notification;
            Log::info('Notification Received: ', (array) $notif);

            // Mengambil data yang diperlukan dari payload
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            // Select reservasi
            $reservation = UserTransaction::where('order_id', $orderId)->first();

            if (! $reservation) {
                Log::error('Reservation not found for order ID: '.$orderId);

                return response('Reservation Not Found', 404);
            }

            // menghandel transaksi berdasarkan status
            switch ($transaction) {
                case 'capture':
                    Log::info('Transaction capture');
                    if ($type == 'credit_card') {
                        if ($fraud == 'challenge') {
                            Log::info("Transaction $orderId challenged by FDS");
                            try {
                                $response = \Midtrans\Transaction::deny($orderId);
                                Log::info( "Transaction $orderId has been denied.");
                            } catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }
                            $reservation->setFailed();
                        } else {
                            Log::info("Transaction $orderId successful with credit card");
                            try {
                                $response = \Midtrans\Transaction::approve($orderId);
                                Log::info( "Transaction $orderId has been accepted.");
                            } catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }
                            $reservation->setSuccess();
                        }
                    }
                    break;

                case 'settlement':
                    Log::info('Transaction settlement');
                    $reservation->setSuccess();
                    break;

                case 'pending':
                    Log::info('Transaction pending');
                    $reservation->setPending();
                    break;

                case 'deny':
                    Log::info('Transaction denied');
                    $reservation->setFailed();
                    break;

                case 'expire':
                    Log::info('Transaction expired');
                    $reservation->setExpired();
                    break;

                case 'cancel':
                    Log::info('Transaction canceled');
                    $reservation->setFailed();
                    break;

                default:
                    Log::error('Unknown transaction status: '.$transaction);

                    return response('Unknown Status', 400);
            }

            Log::info('Reservation status updated to: '.$reservation->transaction_status);

            return response('Notification Processed', 200);

        } catch (\Exception $e) {
            Log::error('Error processing notification: '.$e->getMessage());

            return response('Notification Failed', 500);
        }
    }
}
