<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'staff' => \App\Http\Middleware\IsStaff::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                
                $statusCode = 422; 
                $message = 'ข้อมูลไม่ถูกต้อง';

                $failedRules = $e->validator->failed();
                foreach ($failedRules as $field => $rules) {
                    if (isset($rules['Unique'])) {
                        $statusCode = 409; 
                        $message = 'ข้อมูลซ้ำกับในระบบ (Conflict)';
                        break;
                    }
                }

                return response()->json([
                    'status_code' => $statusCode,
                    'status'      => 'error',
                    'message'     => $message,
                    'data'        => null,
                    'errors'      => $e->errors()
                ], $statusCode);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status_code' => 401,
                    'status'      => 'error',
                    'message'     => 'Unauthenticated. (กรุณา Login และแนบ Token)',
                    'data'        => null,
                    'errors'      => null
                ], 401);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status_code' => 404,
                    'status'      => 'error',
                    'message'     => 'ไม่พบ URL หรือข้อมูลที่ต้องการ',
                    'data'        => null,
                    'errors'      => null
                ], 404);
            }
        });

    })->create();
