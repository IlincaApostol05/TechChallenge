<?php

namespace App\Repository;

use App\Exception\EmptyFieldException;
use App\Exception\InvalidCsvFormatException;
use App\Exception\LessThan30Exception;
use App\Factory\ExchangeFactory;
use App\Service\SplFileInfoWrapper;
use App\Validator\AttributesValidator;
use App\Validator\csvValidator;

class ExchangeRepository
{
    private SplFileInfoWrapper $splFileInfo;
    private csvValidator $csvValidator;
    private AttributesValidator $AttributesValidator;


    public function __construct(SplFileInfoWrapper $splFileInfo, csvValidator $csvValidator, AttributesValidator $AttributesValidator)
    {
        $this->splFileInfo = $splFileInfo;
        $this->csvValidator = $csvValidator;
        $this->AttributesValidator = $AttributesValidator;
    }

    /**
     * @throws EmptyFieldException
     * @throws InvalidCsvFormatException
     * @throws LessThan30Exception
     */

    public function getDataFromFile(): array
    {
        $dataPoints = [];

        $this->csvValidator->validate($this->splFileInfo);
        $importFile = $this->splFileInfo->openFile('r');

        while (!$importFile->eof()) {
            $line = $importFile->fgets();
            if (preg_match('/[0-9a-zA-Z]/', $line)) {
                $exchange = ExchangeFactory::createFromCsvLine($line);
                $dataPoints[] = $exchange;
            }
        }

        return $dataPoints;
    }

    /**
     * @throws EmptyFieldException
     */

    public function getRandomValues($allDataPoints): array
    {
        foreach ($allDataPoints as $dataPoint) {
            $this->AttributesValidator->validate($dataPoint);

        }
        $dataPoints = [];
        $importFile = $this->splFileInfo->openFile('r');
        $lineCount = count($allDataPoints);
        $startLine = rand(0, max(0, $lineCount - 30));
        $importFile->seek($startLine);

        for ($i = 0; $i < 30 && !$importFile->eof(); $i++) {
            $line = $importFile->fgets();
            $exchange = ExchangeFactory::createFromCsvLine($line);
            $dataPoints[] = $exchange;
        }
        return $dataPoints;
    }

}