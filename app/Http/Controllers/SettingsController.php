<?php

namespace App\Http\Controllers;

use App\Models\D365Token;
use App\Services\D365ItemIssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class SettingsController extends Controller
{
    public function index(): View
    {
        $token = D365Token::latest()->first();

        return view('settings.index', compact('token'));
    }

    public function generateToken(Request $request, D365ItemIssueService $service): JsonResponse
    {
        try {
            $userName = auth()->user()?->name ?? 'manual';
            $service->fetchAndStoreToken($userName);

            $token = D365Token::latest()->first();

            return response()->json([
                'status' => true,
                'message' => 'Token generated successfully.',
                'expires_at' => $token?->expires_at?->toIso8601String(),
                'generated_at_human' => $token?->created_at?->format('d M Y  H:i:s'),
                'expires_at_human' => $token?->expires_at?->format('d M Y  H:i:s'),
                'duration_minutes' => $token ? (int) round($token->created_at->diffInSeconds($token->expires_at) / 60) : 0,
                'seconds_remaining' => $token?->secondsRemaining() ?? 0,
                'generated_by' => $token?->generated_by,
                'full_token' => $token?->access_token,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
