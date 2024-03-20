<?php

namespace App\Service;

use App\Repository\OutlierRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OutlierProcessorService
{
    private SessionInterface $session;
    private OutlierRepository $outlierRepository;

    public function __construct(SessionInterface $session, OutlierRepository $outlierRepository)
    {
        $this->session = $session;
        $this->outlierRepository = $outlierRepository;
    }

    public function processOutliers(int $filesNumber): ?array
    {
        $processedData = $this->session->get('processed_data');
        $processedData1 = $this->session->get('processed_data_1');
        $processedData2 = $this->session->get('processed_data_2');

        if ($filesNumber == 1) {
            $filteredData = $this->outlierRepository->processOutliersForData($processedData,1);
            return $filteredData;
        } else {
            $filteredData1 = $this->outlierRepository->processOutliersForData($processedData1,1);
            $filteredData2 = $this->outlierRepository->processOutliersForData($processedData2,2);
            $filteredData = array_merge($filteredData1, $filteredData2);
            return $filteredData;
        }

    }


}
