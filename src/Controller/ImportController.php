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
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $file->move('var/', 'import.csv');

        $data = $this->exchangeRepository->getDataFromFile();

        // Store data in session
        $this->session->set('processed_data', $data);

        return new JsonResponse($data);
    }

}