<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return array_filter([Gate::allows('admin') ? Actions\CreateAction::make() : null]);
    }

    public function getTabs(): array
    {
        $tabs = DB::table('roles')->get();
        // dd($tabs);
        $tabsArray = [
            'All' => Tab::make('All'),
        ];
        foreach ($tabs as $tab) {
            if (Gate::allows('admin') ? $tab->id != 1 : $tab->id != 1 && $tab->id != 2) {
                $tabsArray[$tab->id] = Tab::make($tab->name)
                    ->query(fn ($query) => $query->where('role_id', $tab->id));
            }
        }
        // dd($tabsArray);

        if (Gate::allows('admin') || Gate::allows('pimpinan')) {
            return $tabsArray;
        }

        return [];
    }
}
