<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    }

    public function render($request, Throwable $exception)
    {
        if($exception instanceof ModelNotFoundException && $request->wantsJson()){
            return response()->json(['message' => "Resource not found", "status"=> "404"], 404);
        }
        if ($exception instanceof  BusinessValidationException) {
            return response()->json(['message' => $exception->getMessage(), "status"=> $exception->getCode()], $exception->getCode());
        }
        if ($exception instanceof ResourceNotFoundException){
            return response()->json(['message' => $exception->getMessage(), 'status' => $exception->getCode()], $exception->getCode());
        }
        if ($exception instanceof  UnAuthorizedException){
            return  response()->json(['message' => $exception->getMessage(), 'status'=>$exception->getCode()], $exception->getCode());
        }

        return parent::render($request, $exception);
    }
}
