<?php

namespace App\Filament\Resources\BookingCustomerResource\Pages;

use App\Filament\Resources\BookingCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookingCustomers extends ListRecords
{
    protected static string $resource = BookingCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
