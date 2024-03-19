<?php

namespace App\Controller;

use App\Exception\FileDoesNotExistException;
use App\Exception\MoreThanTwoCSVException;
use App\Repository\ExchangeRepository;
use App\Validator\AttributesValidator;
use App\Validator\csvValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ImportController extends AbstractController
{
    private SessionInterface $session;
    private ExchangeRepository $exchangeRepository;
    private AttributesValidator $attributesValidator;
    private string $firstFileName='import.csv';
    private string $secondFileName='var/import2.csv';
    private csvValidator $csvValidator;

    public function __construct(ExchangeRepository $exchangeRepository,SessionInterface $session, AttributesValidator $attributesValidator,csvValidator $csvValidator)
    {
        $this->exchangeRepository = $exchangeRepository;
        $this->session = $session;
        $this->attributesValidator = $attributesValidator;
        $this->csvValidator = $csvValidator;
    }

    /**
     * @throws Exception
     */

    #[Route('/api/import', name: 'exchange_import', methods: 'POST')]
    public function importAction(Request $request): JsonResponse
    {
        $files = $request->files->all();

        $this->validateFiles($files);

        switch (count($files)) {
            case 1:
                $this->processSingleFile($files);
                break;
            case 2:
                $this->processMultipleFiles($files);
                break;
            default:
                throw new MoreThanTwoCSVException();
        }

        return new JsonResponse($this->exchangeRepository->getAllExchanges());
    }

    private function validateFiles(array $files): void
    {
        if (count($files) == 0) {
            throw new FileDoesNotExistException();
        } elseif (count($files) > 2) {
            throw new MoreThanTwoCSVException();
        }
    }

    private function processSingleFile(array $files): void
    {
        $file = reset($files);

        $file->move('var/', 'import.csv');
        $allData=$this->exchangeRepository->getDataFromFile($this->firstFileName);
//        print_r($allData);
        $this->processExchangeData($allData, 'processed_data', 1);
    }

    private function processMultipleFiles(array $files): void
    {
        $file1 = reset($files);

        $file1->move('var/', 'import1.csv');
        $data1=$this->exchangeRepository->getDataFromFile($this->firstFileName);
        $this->processExchangeData($data1, 'processed_data_1');

        $file2 = end($files);

        $file2->move('var/', 'import2.csv');
        $data2=$this->exchangeRepository->getDataFromFile($this->secondFileName);
        $this->processExchangeData($data2, 'processed_data_2');
    }

    private function processExchangeData(array $data, string $sessionKey, int $filesNumber = null): void
    {
        foreach ($data as $exchange) {
            $this->attributesValidator->validate($exchange);
        }

        $this->session->set($sessionKey, $data);
        if ($filesNumber !== null) {
            $this->session->set('filesNumber', $filesNumber);
        }
    }


}