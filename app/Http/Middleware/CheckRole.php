<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user has any of the roles specified in the route.
     *
     * @param  string ...$roles The list of allowed role names (e.g., 'CEO', 'Finance Manager')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        $user = Auth::user();

        // 2. Check if the user has a role defined
        // We assume the User model has a relationship named 'role'
        if (!$user->role) {
            return response()->json(['message' => 'Access Denied. User role not defined.'], 403);
        }

        $userRoleName = $user->role->name; 

        // 3. Check if the user's role is in the list of allowed roles
        if (in_array($userRoleName, $roles)) {
            return $next($request); // Access granted: Proceed to the controller
        }

        // 4. Deny access if the role doesn't match
        return response()->json(['message' => 'Access Denied. Insufficient role privileges for this action.'], 403);
    }
}