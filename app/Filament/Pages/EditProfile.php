<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $name;

    public $email;

    public $phone;

    public $password;

    public $password_confirmation;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Profil';

    protected static ?string $title = 'Edit Profil';

    protected static string $view = 'filament.pages.edit-profile';

    protected static ?string $slug = 'edit-profile';

    protected static ?int $navigationSort = 1;

    public function mount(): void
    {

        $user = auth()->user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama')
                        ->placeholder('Masukkan nama lengkap')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->placeholder('Masukkan alamat email')
                        ->required()
                        ->readOnly()
                        ->email(),
                    Forms\Components\TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->placeholder('Masukkan nomor telepon')
                        ->required(),
                ])
                ->columns(2), // Two-column layout

            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Kata Sandi Baru')
                        ->placeholder('Masukkan kata sandi baru')
                        ->password()
                        ->rules(['nullable', 'confirmed', Password::min(6)]),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Konfirmasi Kata Sandi')
                        ->placeholder('Konfirmasi kata sandi baru')
                        ->password()
                        ->rules(['nullable']),
                ])
                ->columns(2),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $user = auth()->user();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if (! empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);

            Filament::auth()->logout();

        }

        Notification::make()
            ->title('Profil berhasil diperbarui!')
            ->success()
            ->send();

        // $this->redirect(url('/admin/edit-profile'));
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manage Account';
    }

    public static function getStaffUrl(): string
    {
        return url('/staff/edit-profile');
    }

    public static function getAdminUrl(): string
    {
        return url('/admin/edit-profile');
    }
}
