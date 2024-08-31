<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditRoom extends EditRecord
{
    use HasRecordNavigation;

    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        $existingActions = [
            Actions\DeleteAction::make(),
        ];

        return array_merge($existingActions, $this->getNavigationActions());
    }
}
