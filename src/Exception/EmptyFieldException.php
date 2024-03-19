<?php

namespace App\Exception;

use Throwable;

class EmptyFieldException extends \Exception
{
    public function __construct(
        string     $message = "Empty field in the file!",
        int        $code = 7,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}