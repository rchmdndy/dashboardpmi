<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    public function verify(Request $request)
{

    $user = User::where('email', $request->route('email'))->first();

    $email = $request->route('email');
    $key = config('app.key');
    $expectedHash = hash_hmac('sha256', $email . $key, $key);

    if (! $request->hasValidSignature() || ! hash_equals($request->route('hash'), $expectedHash)) {
        abort(401);
    }elseif (!$user){
        abort(401);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('home', ['verified' => 1]);
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return redirect()->route('home', ['verified' => 1]);
}



    
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home', ['verified' => 1]);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Link verifikasi sudah dikirim!');
    }
}
