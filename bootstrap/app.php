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
        // Gérer les erreurs d'authentification pour retourner JSON au lieu de rediriger
        $exceptions->respond(function ($response, $exception, $request) {
            // Pour les requêtes API, retourner une réponse JSON appropriée
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login to access this resource.',
                        'error' => 'Unauthorized'
                    ], 401);
                }
            }

            return $response;
        });
    })->create();