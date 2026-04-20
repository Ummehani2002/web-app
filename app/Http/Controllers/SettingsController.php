<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class SettingsController extends Controller
{
    public function apiConfiguration()
    {
        return view('settings.api-configuration.index', [
            'apiBearerToken' => (string) config('services.webapp.api_bearer_token'),
        ]);
    }

    public function generateApiToken(Request $request)
    {
        $plainToken = 'wapp_' . Str::random(48);
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = now()->addHour();

        Cache::put("webapp:api-token:{$tokenHash}", [
            'user_id' => $request->user()?->id,
        ], $expiresAt);

        return response()->json([
            'status' => true,
            'message' => 'Temporary API token generated. Valid for 1 hour.',
            'token' => $plainToken,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }

    public function checkD365Connection()
    {
        $tenantId = (string) config('services.d365.tenant_id');
        $clientId = (string) config('services.d365.client_id');
        $clientSecret = (string) config('services.d365.client_secret');
        $scope = (string) config('services.d365.scope');

        if ($tenantId === '' || $clientId === '' || $clientSecret === '' || $scope === '') {
            return response()->json([
                'status' => false,
                'message' => 'D365 credentials are incomplete in environment settings.',
                'checked_at' => now()->toIso8601String(),
            ], 422);
        }

        $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";

        try {
            $response = Http::asForm()
                ->timeout(20)
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => $scope,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'D365 token request failed.',
                    'http_status' => $response->status(),
                    'checked_at' => now()->toIso8601String(),
                ], 502);
            }

            $expiresIn = (int) $response->json('expires_in', 0);
            $accessToken = (string) $response->json('access_token', '');
            if ($expiresIn <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'D365 token response missing expires_in.',
                    'checked_at' => now()->toIso8601String(),
                ], 502);
            }

            if ($accessToken === '') {
                return response()->json([
                    'status' => false,
                    'message' => 'D365 token response missing access_token.',
                    'checked_at' => now()->toIso8601String(),
                ], 502);
            }

            return response()->json([
                'status' => true,
                'message' => 'D365 connection is healthy. Token fetched successfully.',
                'access_token' => $accessToken,
                'expires_in' => $expiresIn,
                'expires_at' => now()->addSeconds($expiresIn)->toIso8601String(),
                'checked_at' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'D365 connection check failed: ' . $e->getMessage(),
                'checked_at' => now()->toIso8601String(),
            ], 500);
        }
    }
}
