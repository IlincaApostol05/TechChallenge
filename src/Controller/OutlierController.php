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

        $processedData = $this->session->get('processed_data');                         //get data from all csv

        if (!is_array($processedData)) {
            // Handle case where processed data is not an array
            return new JsonResponse(['error' => 'Processed data is not an array'], 400);
        }

        $stockPrices = [];
        foreach ($processedData as $exchange) {
            if ($exchange instanceof Exchange) {
                $stockPrices[] = $exchange->getStockPriceValue();
            } else {
                // Handle case where $exchange is not an instance of Exchange
                return new JsonResponse(['error' => 'Invalid data format in processed data'], 400);
            }
        }


        if(!empty($stockPrices)){                                                            //compute the mean
            $mean = array_sum($stockPrices) / count($stockPrices);
        }
        else
            $mean=0;

        $sumSquaredDifferences = 0;
        foreach ($stockPrices as $price) {
            $sumSquaredDifferences += pow($price - $mean, 2);
        }

        if(!empty($stockPrices)) {
            $standardDeviation = sqrt($sumSquaredDifferences / count($stockPrices));   //compute standard deviation
        }
        else
            $standardDeviation=0;

        // Define outlier threshold as 2 standard deviations from the mean
        $outlierThreshold = 2 * $standardDeviation;                                         //compute the outlier threshold

        $outliers = [];
        foreach ($stockPrices as $price){                                                   //get the outliers array with floats
            if(abs($price - $mean) > $outlierThreshold){
                $outliers[] = $price;
            }
        }

        $output = [];
        $number = 0;
        foreach ($processedData as $dataPoint){//get the objects for those outliners
            if ($dataPoint->getStockPriceValue() == $outliers[$number]) {
                $output[] = $dataPoint;
                if($number<count($outliers)-1)
                    $number += 1;
            }
        }


        $csvFilePaths = [];
        foreach ($outliers as $index=>$outlier){
            $csvFileName = 'outlier_' . $index . '.csv';
            $csvFilePath = 'var/' . $csvFileName;
            $csvFilePaths[] = $csvFilePath;

            $csvFile = fopen($csvFilePath, 'w');
            foreach ($output as $object) {

                $deviation = abs($object->getStockPriceValue() - $mean);
                $percentageDeviation = ($standardDeviation / $outlierThreshold) * 100;

                // Extract object properties into an array
                $data = [$object->getStockId(),$object->getTimestamp(), $object->getStockPriceValue(),$mean,$object->getStockPriceValue()-$mean,$percentageDeviation];
                // Write data array to CSV file
                fputcsv($csvFile, $data);
            }
            fclose($csvFile);
        }

        $responseData = [
            'outliers' => $outliers,
            'data points' => $output
        ];

        return new JsonResponse($responseData);
    }
}