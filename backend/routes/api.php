<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;

function allowedEndpointsByRole(UserRole $role): array
{
    return match ($role) {
        UserRole::Admin => [
            '/api/admin/dashboard',
            '/api/management/reports',
            '/api/user',
            '/api/logout',
        ],
        UserRole::Employer => [
            '/api/employer/dashboard',
            '/api/management/reports',
            '/api/user',
            '/api/logout',
        ],
        UserRole::Candidate => [
            '/api/candidate/dashboard',
            '/api/user',
            '/api/logout',
        ],
    };
}

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role' => ['nullable', 'string', 'in:'.implode(',', UserRole::values())],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'] ?? UserRole::Candidate->value,
    ]);

    $token = $user->createToken('postman-token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully.',
        'user' => $user,
        'token' => $token,
        'allowed_endpoints' => allowedEndpointsByRole($user->role),
    ], 201);
});

Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $validated['email'])->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'message' => 'The provided credentials are incorrect.',
        ], 422);
    }

    $token = $user->createToken('postman-token')->plainTextToken;

    return response()->json([
        'message' => 'Logged in successfully.',
        'user' => $user,
        'token' => $token,
        'allowed_endpoints' => allowedEndpointsByRole($user->role),
    ]);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->get('/my-permissions', function (Request $request) {
    return response()->json([
        'role' => $request->user()->role,
        'allowed_endpoints' => allowedEndpointsByRole($request->user()->role),
    ]);
});

Route::middleware(['auth:sanctum'])->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()?->delete();

    return response()->json([
        'message' => 'Logged out successfully.',
    ]);
});

Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin/dashboard', function (Request $request) {
    return response()->json([
        'message' => 'Welcome admin.',
        'user' => $request->user(),
    ]);
});

Route::middleware(['auth:sanctum', 'role:employer'])->get('/employer/dashboard', function (Request $request) {
    return response()->json([
        'message' => 'Welcome employer.',
        'user' => $request->user(),
    ]);
});

Route::middleware(['auth:sanctum', 'role:candidate'])->get('/candidate/dashboard', function (Request $request) {
    return response()->json([
        'message' => 'Welcome candidate.',
        'user' => $request->user(),
    ]);
});

Route::middleware(['auth:sanctum', 'role:admin,employer'])->get('/management/reports', function (Request $request) {
    return response()->json([
        'message' => 'Accessible by admin and employer roles.',
        'user' => $request->user(),
    ]);
});
