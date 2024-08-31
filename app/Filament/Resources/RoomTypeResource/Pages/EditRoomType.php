<?php

namespace App\Filament\Resources\RoomTypeResource\Pages;

use App\Filament\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditRoomType extends EditRecord
{
    use HasRecordNavigation;

    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return[ 
            Actions\DeleteAction::make()
        ];
    }
}
