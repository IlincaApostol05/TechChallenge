<?php

namespace App\Exception;

use Throwable;

class NotCorrectType extends \Exception
{
    public function __construct(
        string     $message = "The csv file contains values(s) that does not correspond to the correct type:float for stock price and string for stock id!",
        int        $code = 7,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

}