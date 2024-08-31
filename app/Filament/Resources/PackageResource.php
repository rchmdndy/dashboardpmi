<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\Pages\ViewPackage;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

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

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Package Information')
                            ->description('Package Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('price_per_person')
                                    ->required()
                                    ->prefix('Rp. ')
                                    ->numeric(),
                                Forms\Components\TextInput::make('min_person_quantity')
                                    ->required()
                                    ->rules(['integer', 'min:1'])
                                    ->helperText('Minimal orang yang dapat menyewa')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->visibility('public')
                            ->directory('images/packages')
                            ->preserveFilenames()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend(now()->timestamp.'_')
                            )
                            ->imageEditor()
                            ->maxSize(8192)
                            ->columnSpanFull()
                            ->rules(['mimes:jpg,jpeg,png,gif'])
                            ->hiddenOn('view'),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Package option Information')
                            ->description('Package Information')
                            ->schema([
                                Forms\Components\Toggle::make('hasLodgeRoom')
                                    ->default(true)
                                    ->helperText('Apakah paket menyewa ruangan meeting atau tidak'),
                                Forms\Components\Toggle::make('hasMeetingRoom')
                                    ->helperText('Apakah paket menyewa kamar atau tidak'),
                                Forms\Components\MarkdownEditor::make('description')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->visibility('public')
                    ->directory('images/packages')
                    ->preserveFilenames()
                    ->imageEditor()
                    ->maxSize(8192)
                    ->columnSpanFull()
                    ->rules(['mimes:jpg,jpeg,png,gif'])
                    ->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('No.')
                    ->rowIndex()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isGlobal: true),
                Tables\Columns\TextColumn::make('price_per_person')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_person_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('hasLodgeRoom')
                    ->boolean(),
                Tables\Columns\IconColumn::make('hasMeetingRoom')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }
                    )
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->size(80)
                    ->getStateUsing(function (Package $record): string {
                        return $record->image;
                    })
                    ->visibility('public')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->color('Amber'),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => ! in_array(auth()->guard()->user()->role_id ?? null, [2])),
                ]),

            ])
            ->bulkActions(
                array_filter([
                    Gate::allows('admin') ?
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]) : null,
                ]),
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
            'view' => ViewPackage::route('/{record}/view'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPackage::class,
            Pages\EditPackage::class,
        ]);
    }

    // public static function getNavigationItems(): array
    // {
    //     return [
    //         NavigationItem::make('Packages')
    //             ->group('Rooms')
    //             ->icon('heroicon-o-rectangle-stack')
    //             ->url(static::getUrl())
    //             ->visible(fn () => !in_array(auth()->guard()->user()->role_id ?? null, [2, 3])),
    //     ];
    // }

    public static function getNavigationLabel(): string
    {
        return __('Packages');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Rooms';
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name Type Room' => $record->name,
            'Price Per Person' => $record->price_per_person,
            'Min Person' => $record->min_person_quantity,
            'In' => 'Packages',
        ];

    }

    public function getGloballySearchableAttribute(): array
    {
        return [
            'name',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Gate::allows('admin') ? PackageResource::getUrl('edit', ['record' => $record]) : PackageResource::getUrl('view', ['record' => $record]);
    }
}
