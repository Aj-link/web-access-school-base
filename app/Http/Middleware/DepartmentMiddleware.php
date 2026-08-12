<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admin/registrar bypasses department restriction
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if (!$user->department_id) {
            abort(403, 'You are not authorized to access this department\'s resources.');
        }

        return $next($request);
    }
}
