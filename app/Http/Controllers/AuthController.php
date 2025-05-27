<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
   public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json(['message' => 'User registered successfully'], 201);
}

    public function login(Request $request)
    {
        // Step 1: Validate input with detailed rules
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Step 2: Return validation errors (422 Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 3: Attempt login
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Step 4: Issue token
        $token = $user->createToken('freelance-api-token')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }


public function logout(Request $request)
{
    $user = $request->user();

    if (!$user || !$user->currentAccessToken()) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    $user->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out successfully.']);
}

}
