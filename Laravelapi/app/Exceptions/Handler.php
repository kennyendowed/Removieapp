<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
class Handler extends ExceptionHandler
{
  
    /**
     * A list of the exception types that are not reported.
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
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
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
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
          return response()->json([
              'status' => 'Authorization Token is Invalid'
          ], 401);

        }else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
          return response()->json([
              'status' => 'Authorization Token is Expired'
          ], 401);
        }
        else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
          return response()->json([
              'status' => 'Authorization Token blacklisted'
          ], 400);

        }
        else if ($exception instanceof \Tymon\JWTAuth\Exceptions\UserNotDefinedException) {
          return response()->json([
              'status' => 'User Not Defined'
          ], 404);
        }
        elseif ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
          return response()->json([
              'error' => 'Resource not found'
          ], 404);
      }
      elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
       // dd($exception);
        return response()->json([
          'message' => 'Resource not found',
          'error' => 'errors.'.$exception->getStatusCode(),
          'dev_message' =>'Resource not found '.$exception.' error'
      ], 404);
 //  return response()->view('errors.'.$e->getStatusCode(), [], $e->getStatusCode());
}
        else{
          return response()->json([
              'status' => ''.$exception
          ], 404);

        }

        //return parent::render($request, $exception);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }


 


}
