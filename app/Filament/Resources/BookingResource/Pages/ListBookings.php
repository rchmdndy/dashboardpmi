<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use JoseEspinal\RecordNavigation\Traits\HasRecordsList;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    use ExposesTableToWidgets;
    use HasRecordsList;

    protected function getHeaderActions(): array
    {
        return array_filter([Gate::allows('admin') ? Actions\CreateAction::make() : null]);
    }

    protected function getHeaderWidgets(): array
    {
        return BookingResource::getWidgets();
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'), // Tab All untuk semua data
        ];

        $roomTypes = DB::table('room_types')->get();
        // dd($roomTypes);

        foreach ($roomTypes as $roomType) {
            $tabName = str_replace('_', ' ', $roomType->room_type);
            $tabs[$roomType->id] = Tab::make($tabName)
                ->query(function (Builder $query) use ($roomType) {
                    return $query->whereHas('room', function (Builder $query) use ($roomType) {
                        $query->where('room_type_id', $roomType->id); // Menyaring berdasarkan room_type_id di tabel rooms
                    });
                });
        }

        // dd($tabs);

        return $tabs;
    }
}
