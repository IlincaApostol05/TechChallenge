<?php

namespace App\Entity;

class Exchange implements \JsonSerializable
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

    public function jsonSerialize(): array
    {
        return [
            'stock id' => $this->stockId,
            'timestamp' => $this->timestamp,
            'stock Price Value' => $this->stockPriceValue
        ];
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