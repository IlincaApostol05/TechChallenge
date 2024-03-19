<?php

namespace App\Validator;

use App\Entity\Exchange;
use App\Exception\EmptyFieldException;
use App\Exception\InvalidTimestampException;
use App\Exception\NotCorrectType;

class AttributesValidator
{
    /**
     * @throws EmptyFieldException
     * @throws NotCorrectType
     * @throws InvalidTimestampException
     */
    public function validate(Exchange $exchange): void
    {
        $stockId =$exchange->getStockId();
        $timestamp = $exchange->getTimestamp();
        $stockPriceValue = $exchange->getStockPriceValue();

        if (!filter_var($stockPriceValue, FILTER_VALIDATE_FLOAT)) {
            throw new NotCorrectType();
        }


        if(empty($stockId)){
            throw new EmptyFieldException();
        }

        $pattern = '/^\d{1,2}[-\/]\d{1,2}[-\/]\d{4}$/';

        if (!preg_match($pattern, $timestamp)) {
            throw new InvalidTimestampException();
        }

    }



}