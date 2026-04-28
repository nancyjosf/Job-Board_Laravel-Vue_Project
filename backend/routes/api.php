<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobController;

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

/*
|--------------------------------------------------------------------------
| Job Discovery Routes (الخاصة بيكي)
|--------------------------------------------------------------------------
*/
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/jobs', [JobController::class, 'index']); // البحث والفلترة
Route::get('/jobs/{jobListing}', [JobController::class, 'show']); // تفاصيل الوظيفة


/*
|--------------------------------------------------------------------------
| Authentication Routes (شغل شذى)
|--------------------------------------------------------------------------
*/
Route::post('/register', function (Request $request) {
    // ... كود الـ register زي ما هو ...
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
    // ... كود الـ login زي ما هو ...
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

// ... باقي الـ middleware routes (user, logout, dashboards) بتكمل تحت زي ما هي ...
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
// إلخ...
