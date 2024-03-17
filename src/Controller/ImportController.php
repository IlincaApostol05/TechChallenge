<?php

namespace App\Controller;

use App\Repository\ExchangeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{

    private ExchangeRepository $exchangeRepository;

    public function __construct(ExchangeRepository $exchangeRepository)
    {
        $this->exchangeRepository = $exchangeRepository;
    }

    #[Route('/api/import', name: 'exchange_import', methods: 'POST')]
    public function importAction(Request $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $file->move('var/', 'import.csv');

        $this->exchangeRepository->getDataFromFile();

        return new JsonResponse($this->exchangeRepository->getAllExchanges());
    }

}