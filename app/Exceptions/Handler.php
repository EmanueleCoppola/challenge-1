<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                $validator = $exception->validator;

                return response()->json(
                    data: ['errors' => $validator->errors()],
                    status: ResponseCode::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        });

        $this->renderable(function (HttpException $exception, Request $request) {
            $message = $exception->getMessage();

            if (!$message) {
                $message = ResponseCode::$statusTexts[$exception->getStatusCode()];
            }

            if ($request->is('api/*')) {
                return response()->json(
                    data: ['error' => $message],
                    status: $exception->getStatusCode()
                );
            }
        });
    }
}
