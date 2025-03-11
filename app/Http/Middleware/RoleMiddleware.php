<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return response()->json([
                'status'=>false,
                'error' => 'Unauthorized:Not allowed to access!'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
