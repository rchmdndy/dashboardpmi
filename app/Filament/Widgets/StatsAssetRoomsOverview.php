<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsAssetRoomsOverview extends BaseWidget
{
    protected int | string | array $column = 2;

    //bagaimana cara jadikan hanya 2 widget per column



    protected function getStats(): array
    {
        $total_kamar = Room::count();


        $rooms_brokens_items = DB::table('room_assets')
            ->where('isBroken', 1)
            ->count('room_id');


        $rooms_brokens = DB::table('room_assets')
            ->where('isBroken', 1)
            ->distinct('room_id')
            ->count('room_id');
            
        return [
            //
            Stat::make('Total Kamar Yang Rusak Itemnya', $rooms_brokens)
            ->description('Menampilkan Total Kamar Yang Rusak Itemnya')
            ->descriptionIcon('heroicon-o-clipboard-document-check')
            ->chart([50, 89, 55, 53, 44, 80, 92])
            ->color('cyan'),

            Stat::make('Total Kamar', $total_kamar)
            ->description('Menampilkan Total Seluruh Kamar')
            ->descriptionIcon('heroicon-o-clipboard-document-check')
            ->chart([50, 89, 55, 53, 44, 80, 92])
            ->color('purple'),

            Stat::make('Total Item Yang Rusak', $rooms_brokens_items)
            ->description('Menampilkan Total Item Yang Rusak')
            ->descriptionIcon('heroicon-o-clipboard-document-check')
            ->chart([50, 89, 55, 53, 44, 80, 92])
            ->color('Amber')
        ];
    }
}
