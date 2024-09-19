<?php

namespace App\Filament\Resources\BookingCustomerResource\Pages;

use App\Filament\Resources\BookingCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookingCustomer extends EditRecord
{
    protected static string $resource = BookingCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
