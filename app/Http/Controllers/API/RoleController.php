<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Display a listing of roles (Lookup Data).
     * Corresponds to: GET /api/v1/lookup/roles
     */
    public function index(): JsonResponse
    {
        // Fetches all roles, ordered by name, selecting only necessary fields.
        $roles = Role::select('id', 'name', 'permissions')->orderBy('name')->get();

        // Returns roles to the frontend for role-based access control (RBAC) setup[cite: 6].
        return response()->json($roles);
    }

    // Since roles are static lookup data in this context, standard CRUD methods
    // (store, show, update, destroy) are often omitted or heavily restricted.
}
