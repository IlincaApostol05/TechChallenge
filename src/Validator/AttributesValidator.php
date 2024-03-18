<?php

namespace App\Validator;

use App\Entity\Exchange;
use App\Exception\EmptyFieldException;
use App\Exception\InvalidTimestampException;
use App\Exception\NotCorrectType;
use InvalidArgumentException;

class AttributesValidator
{
    /**
     * @throws InvalidTimestampException
     * @throws EmptyFieldException
     * @throws NotCorrectType
     */
    public function validate(Exchange $exchange): void
    {
            $timestamp = $exchange->getTimestamp();
            $stockId = $exchange->getStockId();
            $stockPriceValue = $exchange->getStockPriceValue();

            if(!is_string($stockId)){
                throw new NotCorrectType($stockId);
            }

            if(!is_float($stockPriceValue)){
                throw new NotCorrectType($stockPriceValue);
            }




        // Define the regular expression pattern for the expected format
            $pattern = '/^\d{1,2}[-\/]\d{1,2}[-\/]\d{4}$/'; // d-m-yyyy or d/m/yyyy format

            // Use preg_match to check if the timestamp matches the pattern
            if (!empty($timestamp) && !preg_match($pattern, $timestamp)) {
                throw new InvalidArgumentException("Invalid timestamp format: $timestamp");
            }
    }

}