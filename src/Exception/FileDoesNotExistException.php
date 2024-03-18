<?php

namespace App\Exception;

use Throwable;

class FileDoesNotExistException extends \Exception
{
    public function __construct(
        string     $message = "File does not exist!",
        int        $code = 2,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}