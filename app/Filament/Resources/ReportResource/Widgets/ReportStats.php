<?php

namespace App\Filament\Resources\ReportResource\Widgets;

use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\ReportResource\Pages\ListReports;

class ReportStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;
    

    protected function getTablePage(): string
    {
        return ListReports::class;
    }

    protected function getStats(): array
    {
        // Total seluruh kamar
        $total_rooms_all = Room::count();

        // Hitung jumlah kamar yang pernah dibooking di bulan ini
        $occupancy_per_month = DB::table('bookings')
            ->select(DB::raw('COUNT(DISTINCT room_id) as total_booked_rooms'))
            ->whereMonth('start_date', '=', now()->month)
            ->whereYear('start_date', '=', now()->year)
            ->first();


        // Hitung berapa persen kamar yang terisi per bulan
        $occupancy_persen_per_month = 0; // Set default 0 untuk menghindari pembagian dengan nol

        if ($total_rooms_all > 0 && $occupancy_per_month->total_booked_rooms > 0) {
            $occupancy_persen_per_month = number_format(($occupancy_per_month->total_booked_rooms / $total_rooms_all) * 100, 2);
        }

        return [
            //
            Stat::make('Total Reports', $this->getPageTableQuery()->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->chart([2, 3, 6, 12, 8, 6, 12])
                ->color('Amber'),

            Stat::make('Total Incomes', 'Rp. '.number_format($this->getPageTableQuery()->sum('total_income')))
                ->icon('heroicon-o-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total Booked Rooms', $this->getPageTableQuery()->sum('total_bookings'))
                ->icon('heroicon-o-home')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('Amber'),

            Stat::make('Occupancy Rate per Month', $occupancy_persen_per_month . '%')
                ->icon('heroicon-o-home')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('Amber'),

        ];
    }
}
