<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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

        // 404 - NOT FOUND
        $exceptions->render(function (NotFoundHttpException | RouteNotFoundException $e, Request $request) {
            if ($request->is('*')) {
                return response()->json([
                    'message' => 'not found'
                ], 404);
            }
        });

        // 500 - SERVER ERROR
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('*')) {
                return response()->json([
                    'message' => 'server error',
                    'data' => $e->getMessage()
                ], 500);
            }
        });

        // 403 - NOT AUTHORIZED
        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->is('*')) {
                return response()->json([
                    'message' => 'not authorized'
                ], 403);
            }
        });

    })->create();
