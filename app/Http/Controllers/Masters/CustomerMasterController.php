<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\DataAreaId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerMasterController extends Controller
{
    public const TYPES = ['Individual', 'Corporate', 'Government', 'Other'];

    public const EMIRATES = [
        'Abu Dhabi',
        'Dubai',
        'Sharjah',
        'Ajman',
        'Umm Al Quwain',
        'Ras Al Khaimah',
        'Fujairah',
    ];

    public function index(Request $request): View
    {
        $companyCode = strtoupper(trim((string) $request->query('company', '')));

        $customers = Customer::query()
            ->when($companyCode !== '', fn ($q) => DataAreaId::whereUpperTrimEquals($q, 'company_id', $companyCode))
            ->orderByDesc('created_at')
            ->get();

        return view('masters.customers.index', [
            'customers' => $customers,
            'types' => self::TYPES,
            'emirates' => self::EMIRATES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'company_id' => DataAreaId::normalize((string) $request->input('company_id')),
        ]);

        $validated = $request->validate([
            'company_id' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:50', Rule::in(self::TYPES)],
            'customer_id' => [
                'required',
                'string',
                'max:100',
                Rule::unique('customers', 'customer_id')->where(function ($q) use ($request) {
                    DataAreaId::whereUpperTrimEquals($q, 'company_id', (string) $request->input('company_id'));
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'address_flat' => ['nullable', 'string', 'max:100'],
            'address_building' => ['nullable', 'string', 'max:100'],
            'address_area' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:100'],
            'address_emirates' => ['nullable', 'string', 'max:100', Rule::in(self::EMIRATES)],
            'address_pincode' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Customer::create([
            'company_id' => $validated['company_id'],
            'type' => $validated['type'],
            'customer_id' => trim($validated['customer_id']),
            'name' => trim($validated['name']),
            'address_flat' => $this->trimOrNull($validated['address_flat'] ?? null),
            'address_building' => $this->trimOrNull($validated['address_building'] ?? null),
            'address_area' => $this->trimOrNull($validated['address_area'] ?? null),
            'address_city' => $this->trimOrNull($validated['address_city'] ?? null),
            'address_emirates' => $this->trimOrNull($validated['address_emirates'] ?? null),
            'address_pincode' => $this->trimOrNull($validated['address_pincode'] ?? null),
            'phone' => $this->trimOrNull($validated['phone'] ?? null),
            'email' => $this->trimOrNull($validated['email'] ?? null),
            'created_by' => auth()->id(),
        ]);

        $params = ['company' => DataAreaId::normalize($validated['company_id'])];

        return redirect()->route('masters.customers.index', $params)->with('status', 'Customer created successfully.');
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        $customer->delete();

        $params = ['company' => strtoupper((string) $customer->company_id)];

        return redirect()->route('masters.customers.index', $params)->with('status', 'Customer deleted successfully.');
    }

    private function trimOrNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
