<?php

namespace App\Filament\Resources\BookingCustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class BookingCustomerRelationManager extends RelationManager
{
    protected static string $relationship = 'booking_customer';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer_nik')
            ->columns([
                Tables\Columns\TextColumn::make('booking.user_transaction.order_id')
                ->limit(20),
                Tables\Columns\TextColumn::make('customer_nik'),
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('booking.room.room_name'),
                Tables\Columns\TextColumn::make('booking.start_date')
                ->label('Check In'),
                Tables\Columns\TextColumn::make('booking.end_date')
                ->label('Check Out'),

            ])
            ->filters([
                Tables\Filters\Filter::make('check_in_range')
                    ->form([
                        Forms\Components\DatePicker::make('custom_date')
                            ->label('Custom Date')
                            ->hintAction(
                                Forms\Components\Actions\Action::make('set_today')
                                    ->label('Today')
                                    ->action(function ($get, $set) {
                                        // Set the 'custom_date' field to today's date
                                        $set('custom_date', now());
                                    })
                            ),
                    ])
                    ->columns(1)
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['custom_date'])) {
                            $currentDate = $data['custom_date'];
                            return $query->whereHas('booking', function (Builder $q) use ($currentDate) {
                                $q->whereDate('start_date', '<=', $currentDate)
                                    ->whereDate('end_date', '>=', $currentDate);
                            });
                        }
                        return $query; // Return the unfiltered query if no date is set
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['custom_date'] ?? null) {
                            $indicators['custom_date'] = 'Filtering by ' . Carbon::parse($data['custom_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])

            ->headerActions([
            ])
            ->actions([

            ])
            ->bulkActions([

            ]);
    }
}
