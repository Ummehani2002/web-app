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
}
