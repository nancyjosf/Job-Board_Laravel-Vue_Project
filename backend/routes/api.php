<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\PaymentController;
function allowedEndpointsByRole(UserRole $role): array
{
    return match ($role) {
        UserRole::Admin => [
            '/api/admin/dashboard',
            '/api/profile',
            '/api/logout',
        ],
        UserRole::Employer => [
            '/api/employer/dashboard',
            '/api/profile',
            '/api/logout',
        ],
        UserRole::Candidate => [
            '/api/candidate/dashboard',
            '/api/profile',
            '/api/logout',
        ],
    };
}


Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{jobListing}', [JobController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stripe/intent', [PaymentController::class, 'createStripeIntent']);
});



Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role' => ['nullable', 'string', 'in:' . implode(',', UserRole::values())],
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

    if (!$user || !Hash::check($validated['password'], $user->password)) {
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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);

    Route::put('/profile', [ProfileController::class, 'update']);

});