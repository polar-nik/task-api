<?php

use App\Exceptions\CustomValidationException;
use App\Facades\Answer;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('auth')
                ->namespace('App\Http\Controllers')
                ->name('auth.')
                ->middleware('api')
                ->group(base_path('routes/auth.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            AccessDeniedHttpException::class,
        ]);

        $exceptions->renderable(function (NotFoundHttpException $exception, $request) {
            if ($request->wantsJson()) {
                throw new \App\Exceptions\CustomNotFoundHttpException($exception->getMessage());
            }

            return null;
        });

        $exceptions->renderable(function (ValidationException $exception, $request) {
            if ($request->wantsJson()) {
                throw CustomValidationException::withMessages(
                    $exception->validator->getMessageBag()->getMessages()
                );
            }

            return null;
        });

        $exceptions->renderable(function (AccessDeniedHttpException $exception, $request) {
            if ($request->wantsJson()) {
                throw new \App\Exceptions\CustomAccessDeniedException($exception->getMessage());
            }

            return null;
        });

        $exceptions->renderable(function (AuthenticationException $exception, $request) {
            if ($request->wantsJson()) {
                return Answer::error(
                    'Unauthenticated',
                    ['auth' => 'Unauthenticated.'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return null;
        });
    })->create();
