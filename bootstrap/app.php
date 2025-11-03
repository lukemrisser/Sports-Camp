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
        // Trust proxies for Heroku
        $middleware->trustProxies(at: '*');

        // Register your custom middleware alias here
        $middleware->alias([
            'coach' => \App\Http\Middleware\EnsureUserIsCoach::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'verified.staged' => \App\Http\Middleware\VerifiedStaged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
