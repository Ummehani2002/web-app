<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class ItemSalesTaxGroupMasterController extends Controller
{
    public function index(Request $request)
    {
        $currentCompanyCode = strtoupper(trim((string) $request->query('company', '')));
        $selectedCompany = Company::resolveFromMixed($currentCompanyCode);

        return view('masters.item_sales_tax_group.index', [
            'currentCompanyCode' => strtoupper((string) ($selectedCompany?->company_id ?? $currentCompanyCode)),
        ]);
    }
}
