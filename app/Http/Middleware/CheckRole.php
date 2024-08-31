<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        //fcking array role
        $roleIds = Role::whereIn('name', $role)->pluck('id')->toArray();

        if (! in_array(auth()->user()->role_id, $roleIds)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
