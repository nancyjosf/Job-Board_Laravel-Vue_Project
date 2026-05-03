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

function allowedEndpointsByRole(UserRole $role): array
{
    return match ($role) {
        UserRole::Admin => [
            '/api/admin/dashboard',
            '/api/profile',
            '/api/logout',
        ],
        UserRole::Employer => [
            '/api/employer/stats',
            '/api/employer/jobs',
            '/api/profile',
            '/api/user',
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

    return response()->json([
        'message' => 'User registered successfully.',
        'user' => $user,
        'token' => $user->createToken('postman-token')->plainTextToken,
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
        return response()->json(['message' => 'The provided credentials are incorrect.'], 422);
    }

    return response()->json([
        'message' => 'Logged in successfully.',
        'user' => $user,
        'token' => $user->createToken('postman-token')->plainTextToken,
        'allowed_endpoints' => allowedEndpointsByRole($user->role),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('employer')->group(function () {
        Route::get('/stats', [JobController::class, 'stats']); 
        Route::get('/jobs', [JobController::class, 'myJobs']);         
        Route::post('/jobs', [JobController::class, 'store']);         
        Route::put('/jobs/{job}', [JobController::class, 'update']);   
        Route::delete('/jobs/{job}', [JobController::class, 'destroy']); 
        
        Route::patch('/jobs/{job}/publish', [JobController::class, 'publish']);     
        Route::patch('/jobs/{job}/unpublish', [JobController::class, 'unpublish']); 
        Route::patch('/jobs/{job}/archive', [JobController::class, 'archive']);    
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    });
});