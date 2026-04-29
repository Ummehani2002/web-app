<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategorySyncController extends Controller
{
    public function store(Request $request)
    {
        $company = $this->resolveCompany($request);
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Provide valid company code in `company` or `company_id`.',
            ], 422);
        }

        $request->merge([
            'd365_id' => $request->input('d365_id', $request->input('item_category_id')),
        ]);

        $validated = $request->validate([
            'd365_id' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category = ItemCategory::updateOrCreate(
            [
                'company_id' => $company->id,
                'd365_id' => $validated['d365_id'],
            ],
            [
                'name' => $validated['name'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Item category synced successfully',
            'data' => $category,
        ]);
    }

    public function index(Request $request)
    {
        $company = $this->resolveCompany($request);
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Provide valid company code in `company` or `company_id`.',
            ], 422);
        }

        $rows = ItemCategory::query()
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    private function resolveCompany(Request $request): ?Company
    {
        if ($request->filled('company_id')) {
            return Company::resolveFromMixed($request->input('company_id'));
        }

        $companyCode = strtoupper((string) ($request->input('company') ?? $request->input('company_d365_id') ?? ''));
        if ($companyCode === '') {
            return null;
        }

        return Company::resolveFromMixed($companyCode);
    }
}
