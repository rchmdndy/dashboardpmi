<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Cek apakah access token valid
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (TokenExpiredException $e) {
            // Jika token sudah kadaluwarsa, coba refresh token
            try {
                $refreshToken = $request->cookie('refresh_token');
                $user = User::where('refresh_token', $refreshToken)->first();

                if (! $user) {
                    return response()->json(['error' => 'Invalid refresh token'], 401);
                }

                // Generate access token baru
                $newToken = JWTAuth::fromUser($user);

                // Lanjutkan permintaan dengan token baru
                $response = $next($request);
                $response->headers->set('Authorization', 'Bearer '.$newToken);

                return $response;
            } catch (JWTException $e) {
                return response()->json(['error' => 'Token refresh failed'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token error'], 401);
        }

        return $next($request);
    }
}
