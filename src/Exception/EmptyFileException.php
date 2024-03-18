<?php

namespace App\Exception;

use Throwable;

class EmptyFileException extends \Exception
{
    public function __construct(
        string     $message = "Empty file!",
        int        $code = 2,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}