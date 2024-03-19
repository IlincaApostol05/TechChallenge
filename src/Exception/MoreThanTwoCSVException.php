<?php

namespace App\Exception;

use Throwable;

class MoreThanTwoCSVException extends \Exception
{
    public function __construct(
        string     $message = "The input must be 1 or 2 csv files!",
        int        $code = 2,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}