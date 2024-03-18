<?php

namespace App\Controller;

use App\Entity\Exchange;
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
            // Process outliers for processedData
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

        if (!empty($stockPrices)) {                                                            //compute the mean
            $mean = array_sum($stockPrices) / count($stockPrices);
        } else
            $mean = 0;

        $sumSquaredDifferences = 0;
        foreach ($stockPrices as $price) {
            $sumSquaredDifferences += pow($price - $mean, 2);
        }

        if (!empty($stockPrices)) {
            $standardDeviation = sqrt($sumSquaredDifferences / count($stockPrices));   //compute standard deviation
        } else
            $standardDeviation = 0;

        // Define outlier threshold as 2 standard deviations from the mean
        $outlierThreshold = 2 * $standardDeviation;                                         //compute the outlier threshold

        $outliers = [];
        foreach ($stockPrices as $price) {                                                   //get the outliers array with floats
            if (abs($price - $mean) > $outlierThreshold) {
                $outliers[] = $price;
            }
        }

        $output = [];
        $number = 0;
        if (!empty($outliers)) {
            foreach ($processedData as $dataPoint) {//get the objects for those outliners
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
            $deviation = abs($object->getStockPriceValue() - $mean);
            $percentageDeviation = ($standardDeviation / $outlierThreshold) * 100;

            // Extract object properties into an array
            $data = [$object->getStockId(), $object->getTimestamp(), $object->getStockPriceValue(), $mean, $object->getStockPriceValue() - $mean, $percentageDeviation];
            // Write data array to CSV file
            fputcsv($csvFile, $data);
        }
        fclose($csvFile);


        $responseData = [
            'outliers' => $outliers,
            'data points' => $output
        ];

        print_r($responseData);
    }

}