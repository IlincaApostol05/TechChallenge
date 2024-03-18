<?php

namespace App\Exception;

use Throwable;

class LessThan30Exception extends \Exception
{
    public function __construct(
        string     $message = "The file should contain at least 30 lines!",
        int        $code = 8,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}