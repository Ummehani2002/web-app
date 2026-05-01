<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PoolController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->normalizeLegacyPoolIdInput($request);

        $query = Pool::query();

        if ($request->filled('company_id')) {
            $query->where('company_id', trim((string) $request->input('company_id')));
        }

        if ($request->filled('pool_id')) {
            $query->where('pool_id', trim((string) $request->input('pool_id')));
        }

        return response()->json([
            'status' => true,
            'message' => 'Pools fetched successfully.',
            'data' => $query->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeLegacyPoolIdInput($request);
        $payload = $this->validatePayload($request);
        $pool = Pool::create($payload);

        return response()->json([
            'status' => true,
            'message' => 'Pool created successfully.',
            'data' => $pool,
        ], 201);
    }

    public function show(string $pool): JsonResponse
    {
        $resolved = $this->resolvePool($pool);

        if (! $resolved) {
            return response()->json([
                'status' => false,
                'message' => 'Pool not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Pool fetched successfully.',
            'data' => $resolved,
        ]);
    }

    public function update(Request $request, string $pool): JsonResponse
    {
        $this->normalizeLegacyPoolIdInput($request);
        $resolved = $this->resolvePool($pool);

        if (! $resolved) {
            return response()->json([
                'status' => false,
                'message' => 'Pool not found.',
            ], 404);
        }

        $payload = $this->validatePayload($request, $resolved);
        $resolved->update($payload);

        return response()->json([
            'status' => true,
            'message' => 'Pool updated successfully.',
            'data' => $resolved->fresh(),
        ]);
    }

    public function destroy(string $pool): JsonResponse
    {
        $resolved = $this->resolvePool($pool);

        if (! $resolved) {
            return response()->json([
                'status' => false,
                'message' => 'Pool not found.',
            ], 404);
        }

        $resolved->delete();

        return response()->json([
            'status' => true,
            'message' => 'Pool deleted successfully.',
        ]);
    }

    public function syncFromD365(Request $request): JsonResponse
    {
        $this->normalizeLegacyPoolIdInput($request);

        $validated = $request->validate([
            'pool_id' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'string', 'max:100'],
        ]);

        $pool = Pool::updateOrCreate(
            ['pool_id' => trim($validated['pool_id'])],
            [
                'name' => trim($validated['name']),
                'company_id' => trim($validated['company_id']),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Pool synced successfully.',
            'data' => $pool,
        ]);
    }

    /**
     * Accept legacy JSON key `d365_pool_id` as an alias for `pool_id`.
     */
    private function normalizeLegacyPoolIdInput(Request $request): void
    {
        if ($request->filled('d365_pool_id') && ! $request->filled('pool_id')) {
            $request->merge([
                'pool_id' => $request->input('d365_pool_id'),
            ]);
        }

        $queryBag = $request->query();
        if (isset($queryBag['d365_pool_id']) && ! isset($queryBag['pool_id'])) {
            $request->query->set('pool_id', (string) $queryBag['d365_pool_id']);
        }
    }

    private function resolvePool(mixed $value): ?Pool
    {
        if ($value === null || $value === '') {
            return null;
        }

        $needle = trim((string) $value);
        if ($needle === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $needle)) {
            $byId = Pool::query()->find((int) $needle);

            if ($byId) {
                return $byId;
            }
        }

        return Pool::query()->where('pool_id', $needle)->first();
    }

    private function validatePayload(Request $request, ?Pool $pool = null): array
    {
        $uniqueRule = Rule::unique('pools', 'pool_id');

        if ($pool) {
            $uniqueRule->ignore($pool->id);
        }

        $validated = $request->validate([
            'pool_id' => ['required', 'string', 'max:100', $uniqueRule],
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'string', 'max:100'],
        ]);

        return [
            'pool_id' => trim($validated['pool_id']),
            'name' => trim($validated['name']),
            'company_id' => trim($validated['company_id']),
        ];
    }
}
