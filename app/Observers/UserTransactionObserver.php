<?php

namespace App\Observers;

use App\Jobs\SendWhatsappFailedNotification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Filament\Notifications\Notification;
use App\Jobs\SendWhatsappSuccessNotification;

class UserTransactionObserver
{
    /**
     * Handle the UserTransaction "created" event.
     */
    public function created(UserTransaction $userTransaction): void
    {
        //
        $adminUsers = User::whereNotIn('role_id', [4,5])->get();

        foreach ($adminUsers as $admin) {
            Notification::make()
                ->title('New Transaction: '.$userTransaction->order_id)
                ->body('From: '.$userTransaction->user_email.'Type: '.$userTransaction->channel)
                ->info()
                ->sendToDatabase($admin);
        }
    }

    public function updated(UserTransaction $userTransaction): void
    {
        $userTransaction_booking = Booking::where('user_transaction_id', $userTransaction->id)->get();
        $formatter = \NumberFormatter::create('id_ID', \NumberFormatter::CURRENCY);
        $formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, 'Rp');

        $data = [
            'order_id' => $userTransaction->order_id,
            'name' => $userTransaction->user->name,
            'start_date' => Carbon::parse($userTransaction_booking->first()->start_date)->translatedFormat('l, d F Y'),
            'end_date' => Carbon::parse($userTransaction_booking->first()->end_date)->translatedFormat('l, d F Y'),
            'room_type' => $userTransaction_booking->first()->room->roomType->room_type,
            'rooms' => implode(",", $userTransaction_booking->map(function($booking){
                    return $booking->room->room_name;
                    })->toArray()
                ),
            'total_price' => $formatter->formatCurrency($userTransaction->total_price, "IDR")
        ];

        $transactionLink = URL::to("https://palmerinjateng.id/detailTransaction?id=$userTransaction->id&user_email=$userTransaction->user_email");

        if ($userTransaction->wasChanged('transaction_status') && $userTransaction->transaction_status === 'success') {

            $adminUsers = User::whereNotIn('role_id', [4,5])->get();

            foreach ($adminUsers as $admin) {
                Notification::make()
                    ->title('Transaction Success: '.$userTransaction->order_id)
                    ->body('The transaction by '.$userTransaction->user_email.' has been completed successfully.'.'Type: '.$userTransaction->channel)
                    ->success()
                    ->sendToDatabase($admin); // Mengirim notifikasi
            }

        // Log::info(json_encode($data));

        SendWhatsappSuccessNotification::dispatch(
            $userTransaction->user->phone, 
            $transactionLink, 
            $data
        );
        

        } elseif ($userTransaction->wasChanged('transaction_status') && $userTransaction->transaction_status === 'failed') {

            $adminUsers = User::whereNotIn('role_id', [4,5])->get();

            foreach ($adminUsers as $admin) {
                Notification::make()
                    ->title('Transaction Failed: '.$userTransaction->order_id)
                    ->body('The transaction by '.$userTransaction->user_email.' has been Failed.'.'Type: '.$userTransaction->channel)
                    ->danger()
                    ->sendToDatabase($admin);
            }

            // Log::info(json_encode($data));

            SendWhatsappFailedNotification::dispatch(
                $userTransaction->user->phone, 
                $transactionLink, 
                $data
            );
    
        }
    }
}
