<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemSalesTaxGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemSalesTaxGroupController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Item sales tax groups fetched successfully.',
            'data' => ItemSalesTaxGroup::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tax_item_group' => ['required', 'string', 'max:100', 'unique:item_sales_tax_groups,tax_item_group'],
            'tax_group_name' => ['required', 'string', 'max:255'],
        ]);

        $group = ItemSalesTaxGroup::create([
            'tax_item_group' => trim($validated['tax_item_group']),
            'tax_group_name' => trim($validated['tax_group_name']),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Item sales tax group created successfully.',
            'data' => $group,
        ], 201);
    }

    public function destroy(ItemSalesTaxGroup $item_sales_tax_group): JsonResponse
    {
        $item_sales_tax_group->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item sales tax group deleted successfully.',
        ]);
    }
}
