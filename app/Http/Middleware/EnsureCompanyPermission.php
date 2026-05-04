<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $code = strtoupper(trim((string) $request->query('company', '')));
        if ($code === '') {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Select a company first.');
        }

        $company = Company::query()->whereRaw('UPPER(d365_id) = ?', [$code])->first();
        if (! $company) {
            abort(404);
        }

        if (! $user->hasPermissionForCompany($company, $permission)) {
            return redirect()
                ->route('dashboard', ['company' => $code])
                ->with('warning', 'You do not have access to that area for this company.');
        }

        return $next($request);
    }
}
