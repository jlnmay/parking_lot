<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKioskAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        if ($user->isAttendant() || $user->isSupervisor() || $user->isAdmin()) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden.'], 403);
    }
}