<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('company.enforce_access', false)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ! Schema::hasTable('company_memberships')) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $accessible = $user->accessibleCompanyD365Codes();

        if ($accessible->isEmpty()) {
            if ($request->routeIs('dashboard', 'home')) {
                return $next($request);
            }

            return redirect()
                ->route('dashboard')
                ->with('warning', 'You are not assigned to any company yet. Contact a super administrator.');
        }

        $requested = strtoupper(trim((string) $request->query('company', '')));

        if ($requested !== '' && ! $accessible->contains($requested)) {
            $fallback = $accessible->first();

            return redirect()->to(
                $request->fullUrlWithQuery(['company' => $fallback])
            );
        }

        return $next($request);
    }
}
