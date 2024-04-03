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
        api: __DIR__ . '/../routes/api.php',
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

        // Oculta mensagens adicionais para 404 code
        $exceptions->render(function (NotFoundHttpException | RouteNotFoundException $e, Request $request) {
            if ($request->is('*')) {
                return response()->json([
                    'message' => __('http.404')
                ], 404);
            }
        });

        // Padroniza mensagem de retorno
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('*')) {
                $jsonData = json_decode($e->getMessage());
                return response()->json([
                    'message' => !empty($e->getMessage())
                        ? ($jsonData !== null ? $jsonData : $e->getMessage())
                        : __('http.' . $e->getStatusCode())
                ], $e->getStatusCode());
            }
        });
    })->create();
