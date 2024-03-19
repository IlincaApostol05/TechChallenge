<?php

namespace App\Service;

use App\Exception\EmptyFieldException;
use App\Exception\FileDoesNotExistException;
use App\Exception\InvalidCsvFormatException;
use App\Exception\LessThan30Exception;
use App\Exception\MoreThanTwoCSVException;
use App\Repository\ExchangeRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FileProcessorService
{
    private SessionInterface $session;
    private ExchangeRepository $exchangeRepository;

    public function __construct(SessionInterface $session, ExchangeRepository $exchangeRepository)
    {
        $this->session = $session;
        $this->exchangeRepository = $exchangeRepository;
    }

    /**
     * @throws EmptyFieldException
     * @throws FileDoesNotExistException
     * @throws MoreThanTwoCSVException
     */
    public function processImport(Request $request): void
    {
        $files = $request->files->all();
        $this->validateFiles($files);

        switch (count($files)) {
            case 1:
                $this->processSingleFile($files);
                $this->session->set('filesNumber', 1);
                break;
            case 2:
                $this->processMultipleFiles($files);
                $this->session->set('filesNumber', 2);
                break;
            default:
                throw new MoreThanTwoCSVException();
        }
    }

    /**
     * @throws FileDoesNotExistException
     * @throws MoreThanTwoCSVException
     */
    private function validateFiles(array $files): void
    {
        if (count($files) == 0) {
            throw new FileDoesNotExistException();
        } elseif (count($files) > 2) {
            throw new MoreThanTwoCSVException();
        }
    }

    /**
     * @throws EmptyFieldException
     */
    private function processSingleFile(array $files): void
    {
        $file = reset($files);

        $randomData = $this->getRandomExchanges($file);
        $this->writeRandomDataToCsv('var/random.csv', $randomData);
        $this->session->set('processed_data', $randomData);

    }

    /**
     * @throws EmptyFieldException
     */
    private function processMultipleFiles(array $files): void
    {
        $file1 = reset($files);
        $randomData1 = $this->getRandomExchanges($file1);
        $this->writeRandomDataToCsv('var/random1.csv', $randomData1);
        $this->session->set('processed_data_1', $randomData1);


        $file2 = end($files);
        $randomData2 = $this->getRandomExchanges($file2);
        $this->writeRandomDataToCsv('var/random2.csv', $randomData1);
        $this->session->set('processed_data_2', $randomData2);

    }

    private function writeRandomDataToCsv(string $filename, array $data): void
    {
        $file = fopen($filename, 'w');
        foreach ($data as $exchange) {
            fputcsv($file, [$exchange->getStockId(), $exchange->getTimestamp(), $exchange->getStockPriceValue()]);
        }
        fclose($file);
    }

    /**
     * @throws EmptyFieldException
     * @throws LessThan30Exception
     * @throws InvalidCsvFormatException
     */
    private function getRandomExchanges(UploadedFile $file): array
    {
        $file->move('var/', 'import.csv');
        $allData = $this->exchangeRepository->getDataFromFile();
        return $this->exchangeRepository->getRandomValues($allData);
    }
}
