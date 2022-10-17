<?php

namespace App\Exceptions;

use App\Services\Response\ResponseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //массив исключений, которые можно пропустить
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //регистрируем свои собственные обработчики для исключений
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if($request->wantsJson()) {
                return ResponseService::notFound();
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if($request->wantsJson()) {
                return ResponseService::notAuthorize();
            }
        });
    }
}
