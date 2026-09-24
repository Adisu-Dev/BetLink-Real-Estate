<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($user->status === 'banned') {
            $user->tokens()->delete();
            return response()->json([
                'success' => false,
                'message' => 'Your account has been banned by the administrator. Access is permanently denied.',
            ], 403);
        }

        if ($user->status === 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been temporarily suspended by the administrator. Please contact support.',
            ], 403);
        }

        if (!$user->hasAnyRole($roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Insufficient role.',
            ], 403);
        }

        return $next($request);
    }
}
