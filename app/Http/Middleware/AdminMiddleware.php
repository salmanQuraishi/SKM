<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Please log in to access the Admin Panel.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Your account has been deactivated.');
        }

        if (!$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'You do not have administrative privileges.');
        }

        return $next($request);
    }
}
