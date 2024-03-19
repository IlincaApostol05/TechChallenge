<?php

namespace App\Controller;

use App\Service\OutlierProcessorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OutlierController extends AbstractController
{
    private OutlierProcessorService $outlierProcessorService;
    private SessionInterface $session;

    public function __construct(OutlierProcessorService $outlierProcessorService, SessionInterface $session)
    {
        $this->outlierProcessorService = $outlierProcessorService;
        $this->session = $session;
    }

    #[Route('/api/outlier', name: 'outlier_import', methods: 'GET')]
    public function importAction(): JsonResponse
    {
        $filesNumber = $this->session->get('filesNumber');

        $this->outlierProcessorService->processOutliers($filesNumber);
        return new JsonResponse(['message' => 'Outlier processing completed']);
    }
}
