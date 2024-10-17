<?php

namespace App\Exceptions;

use Throwable;

class ResourceNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (ResourceNotFoundException $e) {
            //
        });
    }
}
