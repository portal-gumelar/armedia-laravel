<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyIspApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('app.isp_api_key');

        if (!$apiKey) {
            // Jika api key tidak diset, tolak semua untuk keamanan
            return response()->json(['message' => 'API Key not configured in server'], 500);
        }

        $providedKey = $request->bearerToken() ?? $request->header('X-API-Key') ?? $request->input('api_key');

        if ($providedKey !== $apiKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
