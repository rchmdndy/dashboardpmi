<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Room;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsAssetBookingsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total_rooms_ordered_per_day =  Booking::whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();


        $user_transaction_id_per_day = Booking::whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->distinct()
            ->pluck('user_transaction_id')
            ->toArray();

        $id_rooms_Checkin_per_day = UserTransaction::whereIn('id', $user_transaction_id_per_day)
            ->where('transaction_status', 'success')
            ->where('verifyCheckIn', 1)
            ->where('verifyCheckOut', 0)
            ->pluck('id');
        $total_rooms_Checkin_per_day = Booking::whereIn('user_transaction_id', $id_rooms_Checkin_per_day)->count();

        $id_rooms_Checkout_per_day = UserTransaction::whereIn('id', $user_transaction_id_per_day)
            ->where('transaction_status', 'success')
            ->where('verifyCheckOut', 1)
            ->pluck('id');
        $total_rooms_Checkout_per_day = Booking::whereIn('user_transaction_id', $id_rooms_Checkout_per_day)->count();

        // $id_rooms_Paid_per_day = UserTransaction::whereIn('id', $user_transaction_id_per_day)
        //     ->where('transaction_status', 'success')
        //     ->pluck('id');
        // $total_rooms_Paid_per_day = Booking::whereIn('user_transaction_id', $id_rooms_Paid_per_day)->count();

        $total_rooms_all = Room::count();

        $total_rooms_available_per_day = $total_rooms_all - $total_rooms_ordered_per_day;
        

        return [
            Stat::make('Total Kamar Checkin Hari Ini', $total_rooms_Checkin_per_day)
                ->description('Menampilkan Total Kamar Checkin Hari Ini')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->chart([50, 89, 55, 53, 44, 80, 92])
                ->color('purple'),

            Stat::make('Total Kamar Dipesan Hari Ini', $total_rooms_ordered_per_day)
                ->description('Menampilkan Total Kamar Dipesan Hari Ini')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->chart([50, 89, 55, 53, 44, 80, 92])
                ->color('cyan'),
            
            Stat::make('Total Kamar Checkout Hari Ini', $total_rooms_Checkout_per_day)
                ->description('Menampilkan Total Kamar Checkout Hari Ini')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->chart([50, 89, 55, 53, 44, 80, 92])
                ->color('Amber'),

            Stat::make('Total Kamar Tersedia Hari Ini', $total_rooms_available_per_day)
                ->description('Menampilkan Total Kamar Tersedia Hari Ini')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->chart([50, 89, 55, 53, 44, 80, 92])
                ->color('green'),
            
        ];
    }
}
