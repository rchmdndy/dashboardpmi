<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Resources\Pages\ViewRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class ViewRoom extends ViewRecord
{
    use HasRecordNavigation;

    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(parent::getActions(), $this->getNavigationActions());
    }
}
