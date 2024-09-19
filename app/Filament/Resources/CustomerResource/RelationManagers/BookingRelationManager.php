<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\CreateAction;

class BookingRelationManager extends RelationManager
{
    protected static string $relationship = 'booking_customer';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_nik')
                    ->label('Customer NIK')
                    ->required()
                    ->debounce(1000)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Check if the customer exists with the given NIK
                        $customer = Customer::where('nik', $state)->first();

                        if ($customer) {
                            $set('name', $customer->name); // Optionally fill the name
                            $set('is_name_fillable', false);
                        } else {
                            $set('name',  null);
                            $set('is_name_fillable', true);

                        }
                    })
                    ->placeholder('Enter Customer NIK'),
                Forms\Components\TextInput::make('name')
                    ->label('Customer Name')
                    ->readOnly(fn ($get) => !$get('is_name_fillable')),

            ]);
    }



    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer_nik')
            ->columns([
                Tables\Columns\TextColumn::make('customer_nik'),
                Tables\Columns\TextColumn::make('customer.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                CreateAction::make()
                    ->before(function (array $data) {
             //saya ingin ketika $data['customer_nik'] ada dalam tabel customer maka update jika tidak ada create
                        Customer::firstOrCreate(
                            ['nik' => $data['customer_nik']], // The condition to check (customer_nik)
                            ['name' => $data['name']]         // The values to set if creating a new record
                        );
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
