<?php

namespace App\Http\Controllers\Modules\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class QuotationsController extends Controller
{
    public function index(Request $request)
    {
        $companies = $this->scopedCompaniesQuery($request->user())->orderBy('name')->get();
        if ($companies->isEmpty()) {
            abort(403, 'You do not have access to any organization.');
        }

        $defaultCompany = $companies->first(fn (Company $c) => strtoupper((string) $c->d365_id) === 'PS' || strtoupper((string) $c->name) === 'PS');
        $fallbackCompany = $defaultCompany ?? $companies->first();
        $requestedCompanyCode = strtoupper(trim((string) $request->query('company', '')));
        $selectedCompany = $companies->first(fn (Company $c) => strtoupper((string) $c->d365_id) === $requestedCompanyCode) ?? $fallbackCompany;

        if ($selectedCompany && strtoupper((string) $selectedCompany->d365_id) !== $requestedCompanyCode) {
            return redirect()->route('modules.project-management.quotations', ['company' => strtoupper((string) $selectedCompany->d365_id)]);
        }

        return view('modules.project-management.quotations.index', [
            'companies'          => $companies,
            'currentCompanyCode' => $selectedCompany?->d365_id,
        ]);
    }
}
