<?php

namespace App\Exception;

use Throwable;

class NoFilesException extends \Exception
{
    public function __construct(
        string     $message = "Please provide both name and value(csv file)!",
        int        $code = 6,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}