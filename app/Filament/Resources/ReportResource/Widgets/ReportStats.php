<?php

namespace App\Filament\Resources\ReportResource\Widgets;

use App\Filament\Resources\ReportResource\Pages\ListReports;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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

        ];
    }
}
