<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->orderByDesc('created_at')
            ->get();

        $currentCompanyCode = strtoupper((string) $request->query('company', ''));

        return view('masters.items.index', [
            'items' => $items,
            'currentCompanyCode' => $currentCompanyCode,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'd365_item_id' => ['required', 'string', 'max:50'],
            'item_name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
            'item_category_id' => ['nullable', 'string', 'max:255'],
        ]);

        Item::updateOrCreate(
            ['d365_item_id' => $validated['d365_item_id']],
            [
                'item_name' => $validated['item_name'],
                'type' => $validated['type'] ?? null,
                'item_category_id' => $validated['item_category_id'] ?? null,
            ]
        );

        $company = strtoupper((string) $request->query('company', ''));
        $params = $company !== '' ? ['company' => $company] : [];

        return redirect()
            ->route('masters.items.index', $params)
            ->with('status', 'Item saved successfully.');
    }
}
