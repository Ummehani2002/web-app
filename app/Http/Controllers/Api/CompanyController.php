<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Companies fetched successfully.',
            'data' => $companies,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'company_id' => $request->input('company_id', $request->input('d365_id')),
        ]);

        $validated = $request->validate([
            'company_id' => ['required', 'string', 'max:100', 'unique:companies,d365_id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company = Company::create([
            'company_id' => $validated['company_id'],
            'name' => $validated['name'],
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Company created successfully.',
            'data' => $company,
        ], 201);
    }

    public function show(Company $company)
    {
        return response()->json([
            'status' => true,
            'message' => 'Company fetched successfully.',
            'data' => $company,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $request->merge([
            'company_id' => $request->input('company_id', $request->input('d365_id')),
        ]);

        $validated = $request->validate([
            'company_id' => ['required', 'string', 'max:100', 'unique:companies,d365_id,' . $company->id],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company->update([
            'company_id' => $validated['company_id'],
            'name' => $validated['name'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Company updated successfully.',
            'data' => $company->fresh(),
        ]);
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Company deleted successfully.',
        ]);
    }
}
