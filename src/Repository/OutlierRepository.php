<?php

namespace App\Repository;


class OutlierRepository
{
    public function processOutliersForData(array $processedData,int $index): array
    {
        $stockPrices = $this->extractStockPrices($processedData);
        $mean = $this->calculateMean($stockPrices);
        $standardDeviation = $this->calculateStandardDeviation($stockPrices, $mean);

        $outliers = $this->findOutliers($stockPrices, $mean, $standardDeviation);
        $filteredData = $this->filterOutlierData($processedData, $outliers);
        $this->writeDataToCsv($filteredData, $mean, $standardDeviation, $index);

        return $filteredData;
    }

    private function extractStockPrices(array $processedData): array
    {
        $stockPrices = [];
        foreach ($processedData as $exchange) {
            $stockPrices[] = $exchange->getStockPriceValue();
        }
        return $stockPrices;
    }

    private function calculateMean(array $stockPrices): float
    {
        return array_sum($stockPrices) / count($stockPrices);
    }

    private function calculateStandardDeviation(array $stockPrices, float $mean): float
    {
        $sumSquaredDifferences = 0;
        foreach ($stockPrices as $price) {
            $sumSquaredDifferences += pow($price - $mean, 2);
        }
        return sqrt($sumSquaredDifferences / count($stockPrices));
    }

    private function findOutliers(array $stockPrices, float $mean, float $standardDeviation): array
    {
        $outlierThreshold = 2 * $standardDeviation;
        $outliers = [];
        foreach ($stockPrices as $price) {
            if (abs($price - $mean) > $outlierThreshold) {
                $outliers[] = $price;
            }
        }
        return $outliers;
    }

    private function filterOutlierData(array $processedData, array $outliers): array
    {
        $filteredData = [];
        foreach ($processedData as $dataPoint) {
            if (in_array($dataPoint->getStockPriceValue(), $outliers)) {
                $filteredData[] = $dataPoint;
            }
        }
        return $filteredData;
    }

    private function writeDataToCsv(array $data, float $mean, float $standardDeviation, int $index): void
    {
        $csvFilePath = 'var/' . $index . '.csv';
        $csvFile = fopen($csvFilePath, 'w');

        $outlierThreshold = 2 * $standardDeviation;

        foreach ($data as $object) {
            $deviationFromMean = abs($object->getStockPriceValue() - $mean);

            if ($outlierThreshold > 0) {
                $percentageDeviation = ($deviationFromMean / $outlierThreshold) * 100;
            } else {
                $percentageDeviation = 0;
            }

            $rowData = [
                $object->getStockId(),
                $object->getTimestamp(),
                $object->getStockPriceValue(),
                $mean,
                $standardDeviation,
                $percentageDeviation
            ];
            fputcsv($csvFile, $rowData);
        }
        fclose($csvFile);
    }

}
