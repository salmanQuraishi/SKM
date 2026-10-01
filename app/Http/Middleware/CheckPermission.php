<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized. You do not have permission to access this resource.');
    }
}
