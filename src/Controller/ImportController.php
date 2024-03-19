<?php

namespace App\Controller;

use App\Exception\EmptyFieldException;
use App\Exception\FileDoesNotExistException;
use App\Exception\MoreThanTwoCSVException;
use App\Service\FileProcessorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    private FileProcessorService $fileProcessorService;

    public function __construct(FileProcessorService $fileProcessorService)
    {
        $this->fileProcessorService = $fileProcessorService;
    }

    #[Route('/api/import', name: 'exchange_import', methods: 'POST')]
    public function importAction(Request $request): JsonResponse
    {
        try {
            $this->fileProcessorService->processImport($request);
            return new JsonResponse(['message' => 'Import processing completed']);
        } catch (FileDoesNotExistException|MoreThanTwoCSVException|EmptyFieldException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
