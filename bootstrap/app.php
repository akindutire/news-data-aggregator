<?php

use App\Exceptions\NewsException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
             if($e instanceof NewsException) {

                if ($request->is('api/*')) {
                    return response()->json(['message' => "News Exception/".$e->getMessage(), 'type' =>'NewsException', 'data' => [], 'status' => 'error'], 400);
                }

            } else if ($e instanceof NotFoundHttpException) {
                if ($request->is('api/*')) {
                    return response()->json(['message' => 'Request Not Found!', 'data' => [], 'status' => 'error'], 404);
                }
            } else {
                if ($request->is('api/*')) {
                    return response()->json(['message' =>$e->getMessage(), 'type' =>'GenericException', 'data' => [], 'status' => 'error'], 400);
                }
            }

        });


    })->create();
