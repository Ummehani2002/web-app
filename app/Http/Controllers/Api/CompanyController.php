<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Companies fetched successfully.',
            'data' => Company::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'd365_id' => ['required', 'string', 'max:100', 'unique:companies,d365_id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company = Company::create([
            'd365_id' => $validated['d365_id'],
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
        $validated = $request->validate([
            'd365_id' => ['required', 'string', 'max:100', 'unique:companies,d365_id,' . $company->id],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company->update($validated);

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
