<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiBearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = (string) config('services.webapp.api_bearer_token');
        $providedToken = (string) $request->bearerToken();

        if ($expectedToken === '' || !hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Invalid bearer token.',
            ], 401);
        }

        return $next($request);
    }
}
