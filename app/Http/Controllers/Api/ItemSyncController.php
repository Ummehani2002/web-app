<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemSyncController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'd365_item_id' => 'required|string|max:50',
            'item_name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'item_category_id' => 'nullable|string|max:255',
        ]);

        $item = Item::updateOrCreate(
            ['d365_item_id' => $validated['d365_item_id']],
            [
                'item_name' => $validated['item_name'],
                'type' => $validated['type'] ?? null,
                'item_category_id' => $validated['item_category_id'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Item synced successfully',
            'data' => $item,
        ]);
    }
}
