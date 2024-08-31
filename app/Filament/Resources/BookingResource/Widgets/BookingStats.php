<?php

namespace App\Filament\Resources\BookingResource\Widgets;

use App\Filament\Resources\BookingResource\Pages\ListBookings;
use App\Models\Booking;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BookingStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListBookings::class; // Adjust based on actual requirement
    }

    protected function getStats(): array
    {
        $orderData = Trend::model(Booking::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Booked Rooms', $this->getPageTableQuery()->whereHas('room', function ($query) {
                $query->whereIn('room_type_id', [1, 2]);
            })->count())
                ->icon('heroicon-o-calendar'),

            Stat::make('All Booked Rooms', $this->getPageTableQuery()->count())
                ->chart(
                    $orderData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->icon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Booked Meeting Rooms', $this->getPageTableQuery()->whereHas('room', function ($query) {
                $query->whereIn('room_type_id', [3, 4, 5, 6]);
            })->count())
                ->icon('heroicon-o-calendar'),

        ];

    }
}
