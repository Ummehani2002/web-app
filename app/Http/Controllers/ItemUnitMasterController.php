<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemUnitMasterController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()->orderBy('name')->get();
        $currentCompanyCode = strtoupper(trim((string) $request->query('company', '')));
        $selectedCompany = $companies->first(function ($c) use ($currentCompanyCode) {
            return strtoupper((string) $c->company_id) === $currentCompanyCode;
        }) ?? $companies->first();

        $items = Item::query()
            ->when($selectedCompany, fn ($q) => $q->where('company_id', $selectedCompany->id))
            ->orderBy('item_name')
            ->get(['id', 'd365_id', 'd365_item_id', 'item_name']);

        return view('masters.unit.index', [
            'companies' => $companies,
            'items' => $items,
            'currentCompanyCode' => strtoupper((string) ($selectedCompany->company_id ?? $currentCompanyCode)),
            'selectedCompanyId' => $selectedCompany?->id,
        ]);
    }
}
