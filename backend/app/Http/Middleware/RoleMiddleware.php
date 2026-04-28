<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->jsonError('Unauthenticated.', Response::HTTP_UNAUTHORIZED);
        }

        if ($roles === []) {
            return $this->jsonError('No roles were provided to the middleware.', Response::HTTP_FORBIDDEN);
        }

        $allowedRoles = array_map(
            static fn (string $role): string => strtolower(trim($role)),
            $roles,
        );

        $userRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if (! in_array($userRole, $allowedRoles, true)) {
            return $this->jsonError('You are not authorized to access this resource.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    private function jsonError(string $message, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}