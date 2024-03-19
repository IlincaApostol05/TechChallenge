<?php

namespace App\Exception;

use Throwable;

class FileDoesNotExistException extends \Exception
{
    public function __construct(
        string     $message = "File does not exist or no file provided!",
        int        $code = 6,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}