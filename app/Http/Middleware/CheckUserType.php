<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|array  $userTypes
     */
    public function handle(Request $request, Closure $next, ...$userTypes): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // If no user types are specified, allow all authenticated users
        if (empty($userTypes)) {
            return $next($request);
        }

        // Check if user's type matches any of the allowed types
        if (in_array($user->user_type, $userTypes)) {
            return $next($request);
        }

        // User doesn't have the required user type
        abort(403, 'Unauthorized access. You do not have permission to access this resource.');
    }
}
