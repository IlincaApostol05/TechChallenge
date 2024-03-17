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

    public function jsonSerialize()
    {
        return[
            'stock id' => $this->stockId,
            'timestamp' => $this->timestamp,
            'stock Price Value' => $this->stockPriceValue
        ];
    }

    /**
     * @return string
     */
    public function getStockId(): string
    {
        return $this->stockId;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @return float
     */
    public function getStockPriceValue(): float
    {
        return $this->stockPriceValue;
    }


}