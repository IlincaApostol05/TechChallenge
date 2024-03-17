<?php

namespace App\Factory;

use App\Entity\Exchange;

class ExchangeFactory
{
    public static function createFromCsvLine(string $line): ?Exchange
    {
        $data = explode(',', $line);
        if (count($data) === 3) {
            $stockId = trim($data[0]);
            $timestamp = trim($data[1]);
            $stockPriceValue = floatval(trim($data[2]));
            return new Exchange($stockId, $timestamp, $stockPriceValue);
        }
        return null; // Invalid data format
    }
}