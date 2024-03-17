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

        $stockPrices = [];
        foreach ($processedData as $exchange){
            if($exchange instanceof Exchange){
                $stockPrices[] = $exchange->getStockPriceValue();
            }
        }

        if(!empty($stockPrices)){
            $mean = array_sum($stockPrices) / count($stockPrices);
        }
        else
            $mean=0;

        $sumSquaredDifferences = 0;
        foreach ($stockPrices as $price) {
            $sumSquaredDifferences += pow($price - $mean, 2);
        }
        $standardDeviation = sqrt($sumSquaredDifferences / count($stockPrices));

        // Define outlier threshold as 2 standard deviations from the mean
        $outlierThreshold = 2 * $standardDeviation;

        $outliers = [];
        foreach ($stockPrices as $price){
            if(abs($price - $mean) > $outlierThreshold){
                $outliers[] = $price;
            }

        }



        $output=[];

        foreach ($processedData as $dataPoint){
            $number = 0;
            if($dataPoint->getStockPriceValue() == $outliers[$number]){
                $output[]=$dataPoint;
                $number+=1;
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