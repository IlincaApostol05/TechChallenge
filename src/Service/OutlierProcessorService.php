<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OutlierProcessorService
{
    private SessionInterface $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function processOutliers(int $filesNumber): void
    {
        $processedData = $this->session->get('processed_data');
        $processedData1 = $this->session->get('processed_data_1');
        $processedData2 = $this->session->get('processed_data_2');

        if ($filesNumber == 1) {
            $this->processOutliersForData($processedData, 0);
        } else {
            $this->processOutliersForData($processedData1, 1);
            $this->processOutliersForData($processedData2, 2);
        }
    }

    private function processOutliersForData(array $processedData, int $index): void
    {
        $stockPrices = $this->extractStockPrices($processedData);
        $mean = $this->calculateMean($stockPrices);
        $standardDeviation = $this->calculateStandardDeviation($stockPrices, $mean);

        $outliers = $this->findOutliers($stockPrices, $mean, $standardDeviation);

        $filteredData = $this->filterOutlierData($processedData, $outliers);

        $this->writeDataToCsv($filteredData, $mean, $standardDeviation, $index);
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
