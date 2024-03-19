<?php

namespace App\Entity;

class Exchange
{
    private string $stockId;
    private string $timestamp;
    private float $stockPriceValue;

    public function __construct(string $stockId, string $timestamp, float $stockPriceValue)
    {
        $this->stockId = $stockId;
        $this->timestamp = $timestamp;
        $this->stockPriceValue = $stockPriceValue;
    }

    public function getStockId(): string
    {
        return $this->stockId;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getStockPriceValue(): float
    {
        return $this->stockPriceValue;
    }

}