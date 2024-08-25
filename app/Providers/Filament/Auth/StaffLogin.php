<?php

namespace App\Providers\Filament\Auth;

use Filament\Pages\Auth\Login;
use Illuminate\Contracts\Support\Htmlable;

class StaffLogin extends Login
{
    public function getHeading(): string|Htmlable
    {
        return __('Staff Login');
    }
}
