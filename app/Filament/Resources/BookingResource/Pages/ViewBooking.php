<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\ViewRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class ViewBooking extends ViewRecord
{
    use HasRecordNavigation;

    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(parent::getActions(), $this->getNavigationActions());
    }
}
