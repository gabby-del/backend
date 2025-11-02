<?php

/*namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{

}*/
// app/Http/Controllers/Api/DepartmentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department; // Ensure you import the Model

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource (The 'lookup/departments' route)
     */
    public function index()
    {
        // Simple fetch of all departments
        $departments = Department::select('id', 'name', 'code')->get();

        return response()->json($departments);
    }

    // ... Other methods like store, show, update, destroy would be here if needed
}
