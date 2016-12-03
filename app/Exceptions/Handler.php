<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \Tymon\JWTAuth\Exceptions\TokenExpiredException::class,
        \Tymon\JWTAuth\Exceptions\TokenInvalidException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($request->wantsJson()) {
            return $this->handleApi($request, $exception);
        }

        return $this->handleWeb($request, $exception);
    }

    /**
     * Handle web request exception
     *
     * @param $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response
     */
    private function handleWeb($request, Exception $exception) {
        $bypass = false;

        // Bypass assets
        if($request->is('assets/*') || $request->is('favicon.ico')) {
            $bypass = true;
        }

        if($exception instanceof NotFoundHttpException) {
            // redirect 404 error (HTTP not found) to bootstrap.blade.php (let ng2 handle itself)
            if(!$bypass) {
                return response()->view('bootstrap')->header('Content-Type', 'text/html');
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle api request exception
     *
     * @param $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response
     */
    private function handleApi($request, Exception $exception) {
        if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return response()->json(['message' => 'token_expired'], $exception->getStatusCode());
        } else if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return response()->json(['message' => 'token_invalid'], $exception->getStatusCode());
        } else if($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
            return response()->json(['message' => 'token_absent'], $exception->getStatusCode());
        }

        if(env('APP_DEBUG')) {
            return parent::render($request, $exception);
        } else {
            if($exception instanceof NotFoundHttpException) {
                return response()->json(['message' => 'invalid_request'], $exception->getStatusCode());
            } else {
                return parent::render($request, $exception);
            }
        }
    }
}
