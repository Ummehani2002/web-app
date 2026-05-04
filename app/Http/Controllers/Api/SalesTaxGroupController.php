<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesTaxGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesTaxGroupController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Sales tax groups fetched successfully.',
            'data' => SalesTaxGroup::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tax_group_id' => ['required', 'string', 'max:100', 'unique:sales_tax_groups,tax_group_id'],
            'tax_group_name' => ['required', 'string', 'max:255'],
        ]);

        $group = SalesTaxGroup::create([
            'tax_group_id' => trim($validated['tax_group_id']),
            'tax_group_name' => trim($validated['tax_group_name']),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sales tax group created successfully.',
            'data' => $group,
        ], 201);
    }

    public function destroy(SalesTaxGroup $sales_tax_group): JsonResponse
    {
        $sales_tax_group->delete();

        return response()->json([
            'status' => true,
            'message' => 'Sales tax group deleted successfully.',
        ]);
    }
}
