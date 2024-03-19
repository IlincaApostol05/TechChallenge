<?php

namespace App\Exception;

use Throwable;

class InvalidTimestampException extends \Exception
{
    public function __construct(
        string     $message = "Invalid timestamp format!",
        int        $code = 4,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}