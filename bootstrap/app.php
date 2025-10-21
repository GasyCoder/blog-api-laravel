<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Désactiver CSRF pour les routes API
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Pour les requêtes API, retourner 401 au lieu de rediriger vers login
        $middleware->redirectGuestsTo(function ($request) {
            // Pour une API pure, on retourne toujours null pour déclencher une erreur 401
            // au lieu de rediriger vers une page de login
            return null;
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();