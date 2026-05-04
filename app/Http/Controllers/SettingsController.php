<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\D365Token;
use App\Models\Role;
use App\Models\User;
use App\Services\D365ItemIssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class SettingsController extends Controller
{
    public function tokenIndex(): \Illuminate\View\View
    {
        $token = D365Token::latest()->first();

        return view('settings.token', compact('token'));
    }

    public function credsIndex(): \Illuminate\View\View
    {
        $creds = AppSetting::d365Creds();

        return view('settings.credentials', compact('creds'));
    }

    public function rolesPermissionsIndex(Request $request): View
    {
        if (! Schema::hasTable('permissions')) {
            return view('settings.roles-permissions', [
                'rbacReady' => false,
                'hasCompany' => false,
                'selectedCompany' => null,
                'companyQuery' => [],
                'roles' => collect(),
                'presetRoles' => collect(),
                'memberships' => collect(),
                'canManage' => false,
                'allUsers' => collect(),
                'roleTableOptions' => collect(),
            ]);
        }

        $companyCode = strtoupper(trim((string) $request->query('company', '')));
        $companyQuery = $companyCode !== '' ? ['company' => $companyCode] : [];

        $company = $companyCode !== ''
            ? Company::query()->whereRaw('UPPER(d365_id) = ?', [$companyCode])->first()
            : null;
        if ($company === null) {
            $company = Company::query()->orderBy('name')->first();
        }

        if ($company === null) {
            return view('settings.roles-permissions', [
                'rbacReady' => true,
                'hasCompany' => false,
                'selectedCompany' => null,
                'companyQuery' => [],
                'roles' => collect(),
                'presetRoles' => collect(),
                'memberships' => collect(),
                'canManage' => false,
                'allUsers' => collect(),
                'roleTableOptions' => collect(),
            ]);
        }

        if ($companyCode === '' && $company->d365_id) {
            $companyQuery = ['company' => strtoupper((string) $company->d365_id)];
        }

        Role::ensurePresetRoles($company);

        $roles = Role::query()
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->with('permissions')
            ->get();

        $presetSlugs = ['admin', 'user', 'store_keeper'];
        $presetRoles = $roles
            ->filter(fn (Role $r) => in_array($r->slug, $presetSlugs, true))
            ->sortBy(fn (Role $r) => array_search($r->slug, $presetSlugs, true));

        $memberships = CompanyMembership::query()
            ->where('company_id', $company->id)
            ->with(['user', 'roles'])
            ->orderBy('id')
            ->get();

        $user = $request->user();
        $canManage = $user && ($user->canAccessAdminScreens() || $user->canManageCompanyUsers($company));

        $allUsers = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $legacyRoles = $memberships
            ->flatMap(fn (CompanyMembership $m) => $m->roles)
            ->filter(fn (Role $r) => ! in_array($r->slug, $presetSlugs, true))
            ->unique('id');
        $roleTableOptions = $presetRoles->merge($legacyRoles)->unique('id');

        return view('settings.roles-permissions', [
            'rbacReady' => true,
            'hasCompany' => true,
            'selectedCompany' => $company,
            'companyQuery' => $companyQuery,
            'roles' => $roles,
            'presetRoles' => $presetRoles,
            'roleTableOptions' => $roleTableOptions,
            'memberships' => $memberships,
            'canManage' => $canManage,
            'allUsers' => $allUsers,
        ]);
    }

    public function storeUserAccount(Request $request): RedirectResponse
    {
        if (! $request->user()?->canAccessAdminScreens()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ]);

        $existing = User::query()->where('email', $validated['email'])->first();
        if ($existing) {
            $existing->name = $validated['name'];
            if (! empty($validated['password'])) {
                $existing->password = Hash::make($validated['password']);
            }
            $existing->save();
            $message = 'User updated.';
        } else {
            User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? Str::random(32)),
            ]);
            $message = 'New user created. They can sign in with this email (and password if you set one, or Microsoft).';
        }

        $q = [];
        if ($request->filled('company')) {
            $q['company'] = strtoupper(trim((string) $request->input('company')));
        }

        return redirect()
            ->route('settings.roles-permissions', $q)
            ->with('status', $message);
    }

    public function assignCompanyRole(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('company_memberships')) {
            abort(503);
        }

        $validated = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $company = Company::query()->findOrFail($validated['company_id']);
        $actor = $request->user();
        if (! $actor?->canAccessAdminScreens() && ! $actor?->canManageCompanyUsers($company)) {
            abort(403);
        }

        $role = Role::query()
            ->where('id', $validated['role_id'])
            ->where('company_id', $company->id)
            ->firstOrFail();

        if (! in_array($role->slug, ['admin', 'user', 'store_keeper'], true)) {
            return redirect()
                ->back()
                ->withErrors(['role_id' => 'Choose Admin, User, or Store keeper.']);
        }

        $membership = CompanyMembership::query()->updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'company_id' => $company->id,
            ],
            []
        );
        $membership->roles()->sync([$role->id]);

        $q = $company->d365_id ? ['company' => strtoupper((string) $company->d365_id)] : [];

        return redirect()
            ->route('settings.roles-permissions', $q)
            ->with('status', 'Role assigned for this company. That user now has one role here (replacing any previous role for this company).');
    }

    public function updateCompanyMember(Request $request, CompanyMembership $membership): RedirectResponse
    {
        if (! Schema::hasTable('company_memberships')) {
            abort(503);
        }

        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $company = $membership->company;
        $user = $request->user();
        if (! $user?->canAccessAdminScreens() && ! $user?->canManageCompanyUsers($company)) {
            abort(403);
        }

        $role = Role::query()
            ->where('id', $validated['role_id'])
            ->where('company_id', $company->id)
            ->firstOrFail();

        if (! in_array($role->slug, ['admin', 'user', 'store_keeper'], true)) {
            return redirect()
                ->back()
                ->withErrors(['role_id' => 'Choose Admin, User, or Store keeper.']);
        }

        $membership->roles()->sync([$role->id]);

        $q = $company->d365_id ? ['company' => strtoupper((string) $company->d365_id)] : [];

        return redirect()
            ->route('settings.roles-permissions', $q)
            ->with('status', 'Role updated for this user.');
    }

    public function saveCredentials(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'd365_tenant_id' => ['required', 'string', 'max:255'],
            'd365_client_id' => ['required', 'string', 'max:255'],
            'd365_client_secret' => ['required', 'string', 'max:500'],
            'd365_base_url' => ['required', 'string', 'max:500'],
        ]);

        $baseUrl = rtrim($validated['d365_base_url'], '/');
        $scope = $baseUrl.'/.default';

        $existing = AppSetting::d365Creds();
        $changed = $existing['d365_tenant_id'] !== $validated['d365_tenant_id']
            || $existing['d365_client_id'] !== $validated['d365_client_id']
            || $existing['d365_client_secret'] !== $validated['d365_client_secret']
            || $existing['d365_base_url'] !== $baseUrl;

        AppSetting::set('d365_tenant_id', $validated['d365_tenant_id']);
        AppSetting::set('d365_client_id', $validated['d365_client_id']);
        AppSetting::set('d365_client_secret', $validated['d365_client_secret']);
        AppSetting::set('d365_base_url', $baseUrl);
        AppSetting::set('d365_scope', $scope);

        if ($changed) {
            D365Token::query()->delete();
        }

        return response()->json([
            'status' => true,
            'changed' => $changed,
            'message' => $changed
                ? 'Credentials updated. Token cleared - a fresh one will be fetched on the next API call.'
                : 'Credentials saved. Nothing changed - existing token remains active.',
        ]);
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
                'expires_at' => $token->expires_at->toIso8601String(),
                'generated_at_human' => $token->created_at->format('d M Y  H:i:s'),
                'expires_at_human' => $token->expires_at->format('d M Y  H:i:s'),
                'duration_minutes' => (int) round($token->created_at->diffInSeconds($token->expires_at) / 60),
                'seconds_remaining' => $token->secondsRemaining(),
                'generated_by' => $token->generated_by,
                'full_token' => $token->access_token,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
