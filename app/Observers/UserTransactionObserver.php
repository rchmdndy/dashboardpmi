<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserTransaction;
use Filament\Notifications\Notification;

class UserTransactionObserver
{
    /**
     * Handle the UserTransaction "created" event.
     */
    public function created(UserTransaction $userTransaction): void
    {
        //
        $adminUsers = User::whereNotIn('role_id', [4])->get();

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
        //
        if ($userTransaction->wasChanged('transaction_status') && $userTransaction->transaction_status === 'success') {

            $adminUsers = User::whereNotIn('role_id', [4])->get();

            foreach ($adminUsers as $admin) {
                Notification::make()
                    ->title('Transaction Success: '.$userTransaction->order_id)
                    ->body('The transaction by '.$userTransaction->user_email.' has been completed successfully.'.'Type: '.$userTransaction->channel)
                    ->success()
                    ->sendToDatabase($admin); // Mengirim notifikasi
            }
        } elseif ($userTransaction->wasChanged('transaction_status') && $userTransaction->transaction_status === 'failed') {

            $adminUsers = User::whereNotIn('role_id', [4])->get();

            foreach ($adminUsers as $admin) {
                Notification::make()
                    ->title('Transaction Failed: '.$userTransaction->order_id)
                    ->body('The transaction by '.$userTransaction->user_email.' has been Failed.'.'Type: '.$userTransaction->channel)
                    ->danger()
                    ->sendToDatabase($admin);
            }
        }
    }
}
