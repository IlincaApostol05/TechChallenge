<?php

namespace App\Controller;

use App\Repository\ExchangeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ImportController extends AbstractController
{
    private SessionInterface $session;
    private ExchangeRepository $exchangeRepository;

    public function __construct(ExchangeRepository $exchangeRepository,SessionInterface $session)
    {
        $this->exchangeRepository = $exchangeRepository;
        $this->session = $session;
    }

    #[Route('/api/import', name: 'exchange_import', methods: 'POST')]
    public function importAction(Request $request): JsonResponse
    {
        $uploadedFiles = $request->files->all();
        $processedData = [];

        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile) {
                $fileName = $file->getClientOriginalName();
                $file->move('var/', $fileName);

                // Assuming getDataFromFile processes the uploaded file and returns data
                $data = $this->exchangeRepository->getDataFromFile('var/' . $fileName);
                $processedData[] = $data;
            }
        }
        // Store processed data in session
        $this->session->set('processed_data', $processedData);

        return new JsonResponse(['message' => 'Files uploaded and processed successfully']);
    }

}