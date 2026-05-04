<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ItemUnitController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Item units fetched successfully.',
            'data' => ItemUnit::query()->with('item')->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        if (array_key_exists('defination', $input) && ! array_key_exists('definition', $input)) {
            $request->merge(['definition' => $input['defination']]);
        }

        $validated = $request->validate([
            'unit_id' => ['required', 'string', 'max:50'],
            'unit_name' => ['required', 'string', 'max:255'],
            'definition' => ['nullable', 'string', 'max:5000'],
            'item_id' => ['nullable', 'integer', 'exists:items,id'],
            'item_d365_id' => ['nullable', 'string', 'max:100'],
        ]);

        if (empty($validated['item_id']) && empty($validated['item_d365_id'])) {
            throw ValidationException::withMessages([
                'item_id' => ['Provide item_id (items table id from Item master) or item_d365_id (same code as in Item master).'],
            ]);
        }

        $itemPk = (int) ($validated['item_id'] ?? 0);
        if ($itemPk === 0) {
            $code = strtoupper(trim((string) $validated['item_d365_id']));
            $item = Item::query()
                ->where(function ($q) use ($code) {
                    $q->whereRaw('UPPER(TRIM(d365_id)) = ?', [$code])
                        ->orWhereRaw('UPPER(TRIM(d365_item_id)) = ?', [$code]);
                })
                ->first();
            if ($item === null) {
                throw ValidationException::withMessages([
                    'item_d365_id' => ['No item found in Item master with this id.'],
                ]);
            }
            $itemPk = (int) $item->id;
        }

        $unitId = strtoupper(trim($validated['unit_id']));

        $row = ItemUnit::updateOrCreate(
            [
                'item_id' => $itemPk,
                'unit_id' => $unitId,
            ],
            [
                'unit_name' => trim($validated['unit_name']),
                'definition' => isset($validated['definition']) && $validated['definition'] !== ''
                    ? trim($validated['definition'])
                    : null,
            ],
        );

        if ($row->wasRecentlyCreated && auth()->check()) {
            $row->forceFill(['created_by' => auth()->id()])->save();
        }

        return response()->json([
            'status' => true,
            'message' => $row->wasRecentlyCreated
                ? 'Item unit created successfully.'
                : 'Item unit updated successfully.',
            'data' => $row->fresh()->load('item'),
        ], $row->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(ItemUnit $item_unit): JsonResponse
    {
        $item_unit->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item unit deleted successfully.',
        ]);
    }
}
