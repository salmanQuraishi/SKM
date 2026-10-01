<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\CartCountShareMiddleware::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function ($response, $e, $request) {
            if ($response->getStatusCode() === 419) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your session expired. Please refresh the page and try again.',
                        'session_expired' => true,
                    ], 419);
                }

                if ($request->is('cart/coupon/*') || $request->is('cart*')) {
                    return redirect()->route('cart.index')->with('error', 'Session expired. Please enter the coupon code again.');
                }

                return redirect()->back()->with('error', 'Your session expired. Please refresh and try again.');
            }

            return $response;
        });
    })->create();
