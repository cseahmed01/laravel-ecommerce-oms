<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!auth('api')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();
        $roleArray = explode(',', $roles);

        $hasRole = false;
        foreach ($roleArray as $role) {
            $roleName = trim($role);
            // Check if user has any role with this name (regardless of guard)
            $userRoles = $user->roles->pluck('name')->toArray();
            if (in_array($roleName, $userRoles)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}