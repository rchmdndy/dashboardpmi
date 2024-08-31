<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailForgotPasswordJob;
use App\Jobs\SendMailVerificationJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class JWTAUTHController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            $validator = validator::make(request()->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => 4,
            ]);

            $token = JWTAuth::fromUser($user);
            $user['email_verified_at'] = $user->email_verified_at;

            //QUEUE
            SendMailVerificationJob::dispatch($user);

            // NOT QUEUE
            // event(new Registered($user));

            return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'Failed to create a new user'], 500);
        }
    }

    public function registerSocial(Request $request)
    {
        try {
            // Cari user berdasarkan email
            $user = User::where('email', $request->email)->first();

            if ($user) {

            } else {
                // Jika user tidak ditemukan, buat user baru dengan password acak
                $user = User::create([
                    'email' => $request->email,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'role_id' => 4,
                    'email_verified_at' => now(),
                ]);
                SendMailVerificationJob::dispatch($user);

            }
            $user['email_verified_at'] = $user->email_verified_at;

            $token = JWTAuth::fromUser($user);

            return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'Failed to create a new user'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            $user = User::where('email', $request->email)
                ->first();

            if (! $user) {
                return response()->json(['error' => 'User Not Found'], 401);
            }

            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'The email or password entered is incorrect'], 401);
            }

            // $cookie = Cookie('access_token', $token);
            return response()->json([
                'data' => $user,
                'access_token' => $token, 'token_type' => 'Bearer']);

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'Something went wrong'], 500);
        }

    }

    public function logout()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (! $user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            JWTAuth::invalidate(JWTAuth::getToken());

            auth()->logout();
            session()->invalidate();
            session()->flush();

            return response()->json(['message' => 'Successfully logged out']);

        } catch (\Exception $e) {
            Log::error('Error in logout method: '.$e->getMessage());

            return response()->json(['error' => 'User not logged in'], 401);
        }

    }

    public function me()
    {
        try {
            return response()->json(JWTAuth::user());
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
                'token_type' => 'Bearer',
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = JWTAuth::parseToken()->authenticate();

            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'Old password is incorrect'], 400);
            }

            $user->password = Hash::make($request->password);
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

    public function updateProfile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'data' => $user,
                'message' => 'Profile updated successfully',
            ]);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not update profile'], 401);
        }

    }

    public function sendEmailVerificationNotification(Request $request)
    {
        try {
            // $request->user()->sendEmailVerificationNotification();

            //with queue
            SendMailVerificationJob::dispatch($request->user());

            return response()->json(['message' => 'Email verification link sent on your email']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not send email verification link'], 500);
        }

    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = validator::make(request()->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return response()->json(['error' => 'Could not send Forgot Password link'], 400);
            }

            SendMailForgotPasswordJob::dispatch($request->only('email'));

            return response()->json(['message' => 'Forgot Password link sent on your email']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'Could not send Forgot Password link'], 500);
        }

    }
}
