<?php

namespace App\Filament\Resources\RoomAssetResource\Pages;

use App\Filament\Resources\RoomAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomAsset extends EditRecord
{
    protected static string $resource = RoomAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
