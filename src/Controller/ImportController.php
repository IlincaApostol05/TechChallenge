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
    public function importAction(Request $request, SessionInterface $session): JsonResponse
    {
        $files = $request->files->all();

        if (count($files) == 1) {
            $file = $request->files->get('file');
            $file->move('var/', 'import.csv');
            $data = $this->exchangeRepository->getDataFromFile('var/import.csv');

            // Store data in session
            $this->session->set('processed_data', $data);
            $this->session->set('filesNumber', 1);

            return new JsonResponse(['message' => 'File processed']);

        } elseif (count($files) == 2) {
            $file1 = reset($files);
            $file1->move('var/', 'import1.csv');
            $data1 = $this->exchangeRepository->getDataFromFile('var/import1.csv');

            // Store data in session
            $this->session->set('processed_data_1', $data1);

            $file2 = end($files);
            $file2->move('var/', 'import2.csv');
            $data2 = $this->exchangeRepository->getDataFromFile('var/import2.csv');

            // Store data in session
            $this->session->set('processed_data_2', $data2);
            $this->session->set('filesNumber', 2);

            return new JsonResponse(['message' => 'Files processed and session reset']);
        }

        return new JsonResponse();
    }

}