<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Handles user login and token generation.
     * Corresponds to: POST /login
     */
    public function login(Request $request): JsonResponse
    {
        // Validate incoming credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Throw a general exception to prevent timing attacks
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Ensure user is assigned to departments and cost centers for structure mapping[cite: 8].
        if (! $user->role_id || ! $user->department_id) {
            return response()->json([
                'message' => 'User profile incomplete. Contact system admin.',
            ], 403);
        }

        // Generate the Sanctum API token for secure sign-in
        $token = $user->createToken('opex-api-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'role_id']), // Return basic user data
            'token' => $token,
        ]);
    }


}
