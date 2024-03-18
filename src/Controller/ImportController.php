<?php

namespace App\Controller;

use App\Exception\InvalidCsvFormatException;
use App\Exception\MoreThanTwoCSVException;
use App\Exception\NoFilesException;
use App\Repository\ExchangeRepository;
use App\Validator\AttributesValidator;
use App\Validator\csvValidator;
use Exception;
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
    private AttributesValidator $attributesValidator;
    private csvValidator $csvValidator;

    public function __construct(ExchangeRepository $exchangeRepository, SessionInterface $session, AttributesValidator $attributesValidator,csvValidator $csvValidator)
    {
        $this->exchangeRepository = $exchangeRepository;
        $this->session = $session;
        $this->attributesValidator = $attributesValidator;
        $this->csvValidator = $csvValidator;
    }

    /**
     * @throws NoFilesException
     * @throws Exception
     */

    #[Route('/api/import', name: 'exchange_import', methods: 'POST')]
    public function importAction(Request $request): JsonResponse
    {
        $files = $request->files->all();

        if (count($files) == 0) {
            throw new NoFilesException();
        } elseif (count($files) == 1) {
            $file = reset($files);

            $file->move('var/', 'import.csv');
            $this->exchangeRepository->getDataFromFile('var/import.csv');
            $data = $this->exchangeRepository->getAllExchanges();

            foreach ($data as $exchange){
                $this->attributesValidator->validate($exchange);
            }

            $this->session->set('processed_data', $data);
            $this->session->set('filesNumber', 1);

            return new JsonResponse(['message' => 'File processed']);

        } elseif (count($files) == 2) {
            $file1 = reset($files);
            $this->checkIfCsv($file1);

            $file1->move('var/', 'import1.csv');
            $this->exchangeRepository->getDataFromFile('var/import1.csv');
            $data1 = $this->exchangeRepository->getAllExchanges();

            foreach ($data1 as $exchange){
                $this->attributesValidator->validate($exchange);
            }


            $this->session->set('processed_data_1', $data1);

            $file2 = end($files);
            $this->checkIfCsv($file2);

            $file2->move('var/', 'import2.csv');
            $this->exchangeRepository->getDataFromFile('var/import2.csv');
            $data2 = $this->exchangeRepository->getAllExchanges();

            foreach ($data2 as $exchange){
                $this->attributesValidator->validate($exchange);
            }

            $this->session->set('processed_data_2', $data2);
            $this->session->set('filesNumber', 2);

            return new JsonResponse(['message' => 'Files processed and session reset']);
        } else
            throw new MoreThanTwoCSVException();
    }

    /**
     * @throws InvalidCsvFormatException
     */
    public function checkIfCsv(UploadedFile $file): void
    {
        $fileExtension = $file->getClientOriginalExtension();
        if (strtolower($fileExtension) !== 'csv') {
            throw new InvalidCsvFormatException();
        }
    }

}