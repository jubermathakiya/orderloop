<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class TokenExpirationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Check expiration (assuming token expires in 1 hour)
        $expiresAt = Carbon::parse($accessToken->created_at)->addHours(1);
        if (Carbon::now()->greaterThan($expiresAt)) {
            $accessToken->delete(); // Delete expired token
            return response()->json(['message' => 'Token expired'], 401);
        }

        return $next($request);
    }
}
