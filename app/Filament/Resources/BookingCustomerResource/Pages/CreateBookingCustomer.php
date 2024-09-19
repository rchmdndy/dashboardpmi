<?php

namespace App\Filament\Resources\BookingCustomerResource\Pages;

use App\Filament\Resources\BookingCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBookingCustomer extends CreateRecord
{
    protected static string $resource = BookingCustomerResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        dd($data);
    }
}
