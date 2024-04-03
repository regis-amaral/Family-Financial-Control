<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Padroniza retorno para o formato json
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return true;
        });

        // Padroniza mensagem de retorno
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('*')) {
                return response()->json([
                    'message' => !empty($e->getMessage()) ? $e->getMessage() : __('http.' . $e->getStatusCode())
                ], $e->getStatusCode());
            }
        });
    })->create();
