<?php

namespace App\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $this->renderable(function (Throwable $e) {
            return $this->handleException($e);
        });
    }

    public function handleException(Throwable $e): JsonResponse
    {
        if ($e instanceof HttpException) {
            $errorCode = $e->getStatusCode();
            $defaultMessage = \Symfony\Component\HttpFoundation\Response::$statusTexts[$errorCode];
            $errorMessage = $e->getMessage() == "" ? $defaultMessage : $e->getMessage();

            return $this->errorResponse($errorMessage, $errorCode);
        }

        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("Does not exist any instance of {$model} with a given id", Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        if ($e instanceof AuthenticationException) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if ($e instanceof ValidationException) {
            $errors = Arr::flatten($e->validator->errors()->getMessages());

            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($e instanceof ClientException) {
            return $this->errorResponse($e->getResponse()->getBody()->getContents(), $e->getCode());
        }

        if ($e instanceof ConnectionException) {
            return $this->errorResponse("Connection failed");
        }

        if ($e instanceof \BadMethodCallException) {
            return $this->errorResponse("Invalid method");
        }

        if (config('app.debug')) {
            return $this->errorResponse("{$e->getMessage()} on {$e->getFile()} @ {$e->getLine()}");
        }

        return $this->errorResponse("Unexpected error. Please try again later.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function errorResponse($errorMessage, $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(['error' => $errorMessage], $statusCode);
    }
}
