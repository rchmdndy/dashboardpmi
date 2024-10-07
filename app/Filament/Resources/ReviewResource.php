<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Review;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ReviewResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class   ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
    public static function canViewAny(): bool
    {
        return Gate::allows('admin') || Gate::allows('pimpinan') || Gate::allows('customerService');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('Print')
                ->label('Print')
                ->color('red')
                ->action(function (Table $table) {

                    $currentRecords = $table->getRecords();
                    $recordIds = $currentRecords->pluck('id')->toArray();
                    $user = auth()->user();

                    $url = URL::route('reviews.print', ['records' => $recordIds, 'user' => $user]);
                    // dd($recordIds);

                    return redirect()->to($url);
                })
            ])
            ->columns([
                Tables\Columns\TextColumn::make("row_number")
                    ->label("No")
                    ->rowIndex()
                    ->searchable(false),
                Tables\Columns\TextColumn::make("user.name")
                    ->label("Name")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("user_transaction_id")
                    ->label("User Transaction ID")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("review")
                    ->label("Review")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("score")
                    ->label("Score")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("room_type.room_type")
                    ->label("Room Type")
                    ->sortable()
                    ->searchable(),

            ])

            ->filters([
                    Tables\Filters\SelectFilter::make('room_type_id')
                    ->relationship('room_type', 'room_type')
                    ->label('Room Type'),
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort("created_at", "desc");

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return "History";
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
