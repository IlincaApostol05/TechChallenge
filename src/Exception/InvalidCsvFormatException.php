<?php

namespace App\Exception;

use Throwable;

class InvalidCsvFormatException extends \Exception
{
    public function __construct(
        string     $message = "Invalid csv format!",
        int        $code = 3,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}