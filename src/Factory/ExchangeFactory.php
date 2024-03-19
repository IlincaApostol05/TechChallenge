<?php

namespace App\Factory;

use App\Entity\Exchange;
use App\Exception\EmptyFieldException;

class ExchangeFactory
{
    /**
     * @throws EmptyFieldException
     */
    public static function createFromCsvLine(string $line): ?Exchange
    {
        $data = explode(',', $line);
        if (count($data) === 3) {
            $stockId = trim($data[0]);
            $timestamp = trim($data[1]);
            $stockPriceValue = floatval(trim($data[2]));
            return new Exchange($stockId, $timestamp, $stockPriceValue);
        }
        throw new EmptyFieldException();
    }

}