<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApprovedTeacherMiddleware;
use App\Http\Middleware\GiaoVienMiddleware;
use App\Http\Middleware\HocVienMiddleware;
use App\Http\Middleware\ParticipantMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'AdminMiddleware' => AdminMiddleware::class,
            'GiaoVienMiddleware' => GiaoVienMiddleware::class,
            'HocVienMiddleware' => HocVienMiddleware::class,
            'ApprovedTeacherMiddleware' => ApprovedTeacherMiddleware::class,
            'ParticipantMiddleware' => ParticipantMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $error, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }
            if ($error instanceof ValidationException) {
                return null;
            }
            if ($error instanceof AuthenticationException) {
                return null;
            }
            if ($error instanceof ModelNotFoundException) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy dữ liệu.'], 404);
            }
            if ($error instanceof HttpExceptionInterface && $error->getStatusCode() < 500) {
                return response()->json(['status' => false, 'message' => $error->getMessage() ?: 'Yêu cầu không được phép.'], $error->getStatusCode(), $error->getHeaders());
            }
            if ($error instanceof HttpExceptionInterface && $error->getStatusCode() === 503) {
                return response()->json(['status' => false, 'message' => $error->getMessage() ?: 'Dịch vụ chưa sẵn sàng.'], 503);
            }

            return response()->json(['status' => false, 'message' => 'Không thể xử lý yêu cầu lúc này. Vui lòng thử lại.'], 500);
        });
    })->create();
