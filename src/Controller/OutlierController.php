<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OutlierController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/api/outlier', name: 'outlier_import', methods: 'GET')]
    public function importAction(Request $request): JsonResponse
    {
        $processedData = $this->session->get('processed_data');
        $processedData1 = $this->session->get('processed_data_1');
        $processedData2 = $this->session->get('processed_data_2');
        $filesNumber = $this->session->get('filesNumber');

        if ($filesNumber == 1)
            $this->processOutliers($processedData, 0);
        else {
            $this->processOutliers($processedData1, 1);
            $this->processOutliers($processedData2, 2);
        }

        return new JsonResponse(['message' => 'Outlier processing completed']);
    }

    private function processOutliers(array $processedData, int $index): void
    {
        $stockPrices = [];
        foreach ($processedData as $exchange) {
            $stockPrices[] = $exchange->getStockPriceValue();
        }

        $mean = array_sum($stockPrices) / count($stockPrices);

        $sumSquaredDifferences = 0;
        foreach ($stockPrices as $price) {
            $sumSquaredDifferences += pow($price - $mean, 2);
        }

        $standardDeviation = sqrt($sumSquaredDifferences / count($stockPrices));
        $outlierThreshold = 2 * $standardDeviation;

        $outliers = [];
        foreach ($stockPrices as $price) {
            if (abs($price - $mean) > $outlierThreshold) {
                $outliers[] = $price;
            }
        }

        $output = [];
        $number = 0;
        if (!empty($outliers)) {
            foreach ($processedData as $dataPoint) {
                if ($dataPoint->getStockPriceValue() == $outliers[$number]) {
                    $output[] = $dataPoint;
                    if ($number < count($outliers) - 1)
                        $number += 1;
                }
            }
        }

        $csvFilePath = 'var/' . $index . '.csv';
        $csvFile = fopen($csvFilePath, 'w');
        foreach ($output as $object) {
            $percentageDeviation = ($standardDeviation / $outlierThreshold) * 100;
            $data = [$object->getStockId(), $object->getTimestamp(), $object->getStockPriceValue(), $mean, $standardDeviation, $percentageDeviation];

            fputcsv($csvFile, $data);
        }
        fclose($csvFile);
    }

}