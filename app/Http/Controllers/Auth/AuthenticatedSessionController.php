<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;
use Validator;

class AuthenticatedSessionController extends Controller
{
    public function register(Request $request)
{
    try {
        $validator = validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);

    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json(['error' => 'Failed to create a new user'], 500);
    }
}

public function login(Request $request)
    {
        try {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'The email or password entered is incorrect'], 401);
        }

        // $cookie = Cookie('access_token', $token);
        $user = JWTAuth::user();
        
        return response()->json([
            'data' => $user,
            'access_token' => $token, 'token_type' => 'Bearer']);

    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json(['error' => 'Something went wrong'], 500);
    }

    }

    public function logout()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            JWTAuth::invalidate(JWTAuth::getToken());

            auth()->logout();

            return response()->json(['message' => 'Successfully logged out']);

        }catch (\Exception $e) {
            \Log::error('Error in logout method: '.$e->getMessage());
            return response()->json(['error' => 'User not logged in'], 401);}

    }

    public function me()
{
    try {
        return response()->json(JWTAuth::parseToken()->authenticate());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}


public function refresh(Request $request)

    {
        try {
            // Dapatkan token baru dari JWTAuth
            $newToken = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($newToken);

            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'Bearer'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }


    public function updatePassword(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Dapatkan token baru dari JWTAuth
        $newToken = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($newToken);

        return response()->json([
            'message' => 'Password updated successfully',
            'access_token' => $newToken]);

    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['error' => 'Could not Update Password'], 401);
    }

}


}
