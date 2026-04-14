<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Accept numeric database id, or companies.d365_id (e.g. C001).
     */
    private function resolveCompanyDatabaseId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $str = is_string($value) ? trim($value) : (string) $value;
        if ($str === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $str)) {
            $id = (int) $str;
            if (Company::query()->whereKey($id)->exists()) {
                return $id;
            }
        }

        return Company::query()->where('d365_id', $str)->value('id');
    }

    /**
     * Accept numeric project id, or projects.d365_id (e.g. PRJ-002).
     */
    private function resolveProject(mixed $value): ?Project
    {
        if ($value === null || $value === '') {
            return null;
        }

        $str = is_string($value) ? trim($value) : (string) $value;
        if ($str === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $str)) {
            $byId = Project::query()->find((int) $str);
            if ($byId) {
                return $byId;
            }
        }

        return Project::query()->where('d365_id', $str)->first();
    }

    public function index(Request $request)
    {
        $query = Project::with('company:id,name');

        if ($request->filled('company_id')) {
            $resolved = $this->resolveCompanyDatabaseId($request->input('company_id'));
            if ($resolved === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No company found for this id or D365 id.',
                    'errors' => ['company_id' => ['Unknown company.']],
                ], 422);
            }
            $query->where('company_id', $resolved);
        }

        return response()->json([
            'status' => true,
            'message' => 'Projects fetched successfully.',
            'data' => $query->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required'],
            'd365_id' => ['required', 'string', 'max:100', 'unique:projects,d365_id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $companyId = $this->resolveCompanyDatabaseId($validated['company_id']);
        if ($companyId === null) {
            throw ValidationException::withMessages([
                'company_id' => ['No company found for this id or D365 id.'],
            ]);
        }

        $project = Project::create([
            'company_id' => $companyId,
            'd365_id' => $validated['d365_id'],
            'name' => $validated['name'],
            'created_by' => auth()->id(),
        ]);

        $project->load('company:id,name');

        return response()->json([
            'status' => true,
            'message' => 'Project created successfully.',
            'data' => $project,
        ], 201);
    }

    public function show(string $project)
    {
        $project = $this->resolveProject($project);
        if (! $project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found.',
            ], 404);
        }

        $project->load('company:id,name');

        return response()->json([
            'status' => true,
            'message' => 'Project fetched successfully.',
            'data' => $project,
        ]);
    }

    public function update(Request $request, string $project)
    {
        $project = $this->resolveProject($project);
        if (! $project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found.',
            ], 404);
        }

        $validated = $request->validate([
            'company_id' => ['required'],
            'd365_id' => ['required', 'string', 'max:100', 'unique:projects,d365_id,' . $project->id],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $companyId = $this->resolveCompanyDatabaseId($validated['company_id']);
        if ($companyId === null) {
            throw ValidationException::withMessages([
                'company_id' => ['No company found for this id or D365 id.'],
            ]);
        }

        $project->update([
            'company_id' => $companyId,
            'd365_id' => $validated['d365_id'],
            'name' => $validated['name'],
        ]);
        $project->load('company:id,name');

        return response()->json([
            'status' => true,
            'message' => 'Project updated successfully.',
            'data' => $project->fresh(['company:id,name']),
        ]);
    }

    public function destroy(string $project)
    {
        $project = $this->resolveProject($project);
        if (! $project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found.',
            ], 404);
        }

        $project->delete();

        return response()->json([
            'status' => true,
            'message' => 'Project deleted successfully.',
        ]);
    }
}
