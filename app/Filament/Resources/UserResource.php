<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\RelationManagers\BookingRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserTransactionResource\RelationManagers\UserTransactionRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Accounts';

    protected static ?string $recordTitleAttribute = 'email';

    protected static ?int $navigationSort = 0;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function canEdit(Model $record): bool
    {
        return Gate::allows('admin');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::allows('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Section')
                    ->description('Contain User Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->unique(ignorable: function ($record) {
                                return $record;
                            })
                            ->placeholder('Input Email')
                            ->email()
                            ->maxLength(100)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('Input Name')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->placeholder('Input Phone')
                            ->maxLength(30),
                        Forms\Components\DateTimePicker::make('email_verified_at'),
                        Forms\Components\Select::make('role_id')
                            ->default(4)
                            ->label('Role')
                            ->native(false)
                            ->preload()
                            ->options(function () {
                                return \App\Models\Role::where('name', '!=', 'admin')->pluck('name', 'id');
                            }),
                    ])
                    ->columns(2)
                    ->columnSpan(3), // Memberikan lebih banyak ruang ke section ini

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(1) // Lebih sedikit ruang untuk section ini
                    ->hidden(fn (?User $record) => $record === null),

                Section::make('Password Section')
                    ->description('Contain User Information')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->placeholder('Input Password')
                            ->password()
                            ->required()
                            ->minLength(6)
                            ->maxLength(100),
                        // ->visible(fn ($livewire): bool => $livewire instanceof CreateRecord)  // Pilihan kedua
                        Forms\Components\TextInput::make('password_confirmation')
                            ->placeholder('Confirm Password')
                            ->password()
                            ->required()
                            ->same('password')
                            ->maxLength(100),
                    ])
                    ->visibleOn('create'),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('No.')
                    ->rowIndex()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('email_verified_at')
                    ->label('Email Verified')
                    ->form([
                        Forms\Components\Select::make('email_verified_at')
                            ->options([
                                'all' => 'All',
                                'verified' => 'Verified',
                                'unverified' => 'Unverified',
                            ])
                            ->reactive(),
                    ])
                    ->query(function (Builder $query, $data) {
                        if ($data['email_verified_at'] === 'verified') {
                            $query->whereNotNull('email_verified_at');
                        } elseif ($data['email_verified_at'] === 'unverified') {
                            $query->whereNull('email_verified_at');
                        }
                    }),
            ])
            ->actions(
                ActionGroup::make([
                    ViewAction::make(),
                    ...array_filter([
                        Gate::allows('admin') ? EditAction::make()
                            ->color('amber')
                            ->label('Edit')
                            : null,
                        Gate::allows('admin') ? Action::make('toggleEmailVerification')
                            ->label(fn (Model $record) => $record->email_verified_at ? 'Unverify' : 'Verify')
                            ->icon(fn (Model $record) => $record->email_verified_at ? 'heroicon-m-x-circle' : 'heroicon-m-check-badge')
                            ->tooltip(fn (Model $record) => $record->email_verified_at ? 'Click to unverify user email' : 'Click to verify user email')
                            ->color(fn (Model $record) => $record->email_verified_at ? 'danger' : 'success')
                            ->action(function (Model $record) {
                                $record->email_verified_at = $record->email_verified_at ? null : now();
                                $record->save();
                                Notification::make()
                                    ->title('Status berhasil diperbarui!')
                                    ->success()
                                    ->send();
                            })
                            : null,
                    ]),
                ])
            )
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BookingRelationManager::class,
            UserTransactionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}/view'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUser::class,
            Pages\EditUser::class,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        if (Gate::allows('admin')) {
            return parent::getEloquentQuery()
                ->whereIn('role_id', [2, 3, 4]);

        } elseif (Gate::allows('staff')) {
            return parent::getEloquentQuery()
                ->whereIn('role_id', [4]);
        }
        abort(403, 'Unauthorized action.');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->email;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'phone' => $record->phone,
            'Role' => $record->role->name,
            'In' => 'Accounts',
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['role']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Gate::allows('admin') ? UserResource::getUrl('edit', ['record' => $record]) : UserResource::getUrl('view', ['record' => $record]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone', 'role.name'];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manage Account';
    }
}
