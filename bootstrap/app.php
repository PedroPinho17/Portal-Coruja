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
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrar middleware global de segurança
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        $middleware->alias([
            'admin.basic' => \App\Http\Middleware\AdminBasicAuth::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'no_back' => \App\Http\Middleware\PreventBackHistory::class,
            'require.password.change' => \App\Http\Middleware\RequirePasswordChange::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
