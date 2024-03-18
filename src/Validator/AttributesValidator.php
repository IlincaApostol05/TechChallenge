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
            $stockPriceValue = $exchange->getStockPriceValue();

            if (!filter_var($stockPriceValue, FILTER_VALIDATE_FLOAT)){
                throw new NotCorrectType();
            }

            if(!preg_match('/[0-9a-zA-Z]/', $timestamp)){
                throw new EmptyFieldException($timestamp);
            }

            $pattern = '/^\d{1,2}[-\/]\d{1,2}[-\/]\d{4}$/';

            if (empty($timestamp) || !preg_match($pattern, $timestamp)) {
                throw new InvalidArgumentException("Invalid timestamp format: $timestamp");
            }
    }

}