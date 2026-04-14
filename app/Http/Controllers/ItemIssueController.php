<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\D365ItemIssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ItemIssueController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->select(['id', 'd365_id', 'name'])
            ->orderBy('name')
            ->get();

        return view('modules.project-management.item-issue.index', [
            'projects' => $projects,
        ]);
    }

    public function lookupItems(Request $request, D365ItemIssueService $service): JsonResponse
    {
        $validated = $request->validate([
            'DataAreaId' => ['required', 'string', 'max:20'],
            'ItemId' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $data = $service->lookupItems($validated['DataAreaId'], $validated['ItemId'] ?? null);

            return response()->json([
                'status' => true,
                'message' => 'Items fetched from D365.',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Item lookup failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function lookupProjects(Request $request, D365ItemIssueService $service): JsonResponse
    {
        $validated = $request->validate([
            'DataAreaId' => ['required', 'string', 'max:20'],
            'ProjectId' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $data = $service->lookupProjects($validated['DataAreaId'], $validated['ProjectId'] ?? null);

            return response()->json([
                'status' => true,
                'message' => 'Projects fetched from D365.',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Project lookup failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function post(Request $request, D365ItemIssueService $service): JsonResponse
    {
        $validated = $request->validate([
            'DataAreaId' => ['required', 'string', 'max:20'],
            'ItemIssueHeader' => ['required', 'array'],
            'ItemIssueHeader.RequestId' => ['required', 'string', 'max:100'],
            'ItemIssueHeader.Description' => ['required', 'string', 'max:255'],
            'ItemIssueHeader.InventSiteId' => ['required', 'string', 'max:100'],
            'ItemIssueHeader.InventLocationId' => ['required', 'string', 'max:100'],
            'ItemIssueLines' => ['required', 'array', 'min:1'],
            'ItemIssueLines.*.RequestId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.InventSiteId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.InventLocationId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.ProjId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.ProjCategoryId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.ItemId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.ProjSalesCurrencyId' => ['required', 'string', 'max:20'],
            'ItemIssueLines.*.ProjSalesPrice' => ['required', 'numeric'],
            'ItemIssueLines.*.ProjUnitID' => ['required', 'string', 'max:30'],
            'ItemIssueLines.*.ProjTaxGroupId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.ProjTaxItemGroupId' => ['nullable', 'string', 'max:100'],
            'ItemIssueLines.*.Qty' => ['required', 'numeric', 'gt:0'],
            'ItemIssueLines.*.PriceUnit' => ['required', 'numeric', 'gt:0'],
            'ItemIssueLines.*.LineNum' => ['required', 'integer', 'min:1'],
            'ItemIssueLines.*.wMSLocationId' => ['required', 'string', 'max:100'],
            'ItemIssueLines.*.InventSizeId' => ['nullable', 'string', 'max:100'],
            'ItemIssueLines.*.InventSerialId' => ['nullable', 'string', 'max:100'],
            'ItemIssueLines.*.InventStyleId' => ['nullable', 'string', 'max:100'],
        ]);

        $d365Payload = [
            '_request' => $validated,
        ];

        try {
            $result = $service->postItemIssue($d365Payload);
            $journalId = $this->extractJournalId($result);

            return response()->json([
                'status' => true,
                'message' => 'Item issue posted to D365.',
                'journal_id' => $journalId,
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Item issue post failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function extractJournalId(array $result): ?string
    {
        $possibleKeys = [
            'JournalId',
            'JournalID',
            'journalId',
            'ItemJournalId',
            'ItemIssueJournalId',
            'RequestId',
        ];

        foreach ($possibleKeys as $key) {
            if (isset($result[$key]) && is_scalar($result[$key])) {
                return (string) $result[$key];
            }
        }

        foreach (['_response', 'data', 'result'] as $nested) {
            if (!isset($result[$nested]) || !is_array($result[$nested])) {
                continue;
            }

            foreach ($possibleKeys as $key) {
                if (isset($result[$nested][$key]) && is_scalar($result[$nested][$key])) {
                    return (string) $result[$nested][$key];
                }
            }
        }

        return null;
    }
}
