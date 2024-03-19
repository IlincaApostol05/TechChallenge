<?php

namespace App\Repository;

use App\Factory\ExchangeFactory;
use App\Service\SplFileInfoWrapper;
use App\Validator\csvValidator;
use Symfony\Component\Filesystem\Filesystem;

class ExchangeRepository implements \JsonSerializable
{
    private array $exchanges = [];
    private Filesystem $filesystem;
    private SplFileInfoWrapper $splFileInfo;
    private csvValidator $csvValidator;


    public function __construct(Filesystem $filesystem, SplFileInfoWrapper $splFileInfo, csvValidator $csvValidator)
    {
        $this->filesystem = $filesystem;
        $this->splFileInfo = $splFileInfo;
        $this->csvValidator = $csvValidator;
    }

    /**
     * @throws \Exception
     */
    public function getDataFromFile(string $filePath): array
    {
        $dataPoints = [];

        //$this->csvValidator->validate($filePath,$this->splFileInfo);
            $importFile = $this->splFileInfo->openFile('r');

            while (!$importFile->eof()) {
                $line = $importFile->fgets();
                if (preg_match('/[0-9a-zA-Z]/', $line)) {
                    $exchange = ExchangeFactory::createFromCsvLine($line);
                    if ($exchange !== null) {
                        $dataPoints[] = $exchange;
                    }
                }
            }
            $dataPoints = $this->getRandomValues($dataPoints,$importFile);
            //print_r($dataPoints);

        return $dataPoints;
    }

    public function getRandomValues($dataPoints,$importFile):array
    {
        $lineCount = count($dataPoints);
        $startLine = rand(0, max(0, $lineCount - 30));
        $importFile->seek($startLine);

        for ($i = 0; $i < 30 && !$importFile->eof(); $i++) {
            $line = $importFile->fgets();
            if (!empty($line)) {
                $exchange = ExchangeFactory::createFromCsvLine($line);
                if ($exchange !== null) {
                    $dataPoints2[] = $exchange;
                }
            }
        }
        $this->exchanges = array_merge($dataPoints2,$dataPoints2);
        return $dataPoints2;
    }


    public function jsonSerialize(): array
    {
        return $this->exchanges;
    }


    public function getAllExchanges(): array
    {
        return $this->jsonSerialize();
    }
}