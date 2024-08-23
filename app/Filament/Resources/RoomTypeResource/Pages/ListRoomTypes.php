<?php

namespace App\Filament\Resources\RoomTypeResource\Pages;

use App\Filament\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use JoseEspinal\RecordNavigation\Traits\HasRecordsList;

class ListRoomTypes extends ListRecords
{
    use HasRecordsList;

    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
