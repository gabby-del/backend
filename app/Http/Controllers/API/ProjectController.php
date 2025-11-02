<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     * Corresponds to: GET /api/v1/lookup/projects
     */
    public function index(): JsonResponse
    {
        // Return projects for mapping requests and budgets[cite: 26].
        $projects = Project::select('id', 'name', 'department_id')
            ->with('department')
            ->orderBy('name')
            ->get();

        return response()->json($projects);
    }
}
