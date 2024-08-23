<?php

namespace App\Filament\Resources\RoomTypeResource\Pages;

use App\Filament\Resources\RoomTypeResource;
use Filament\Resources\Pages\ViewRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class ViewRoomType extends ViewRecord
{
    use HasRecordNavigation;

    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return[
        ];
    }
}
