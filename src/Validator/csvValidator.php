<?php

namespace Validator;

use App\Exception\InvalidCsvFormatException;
use App\Exception\LessThan30Exception;
use App\Service\SplFileInfoWrapper;


class csvValidator
{
    /**
     * @throws InvalidCsvFormatException
     * @throws LessThan30Exception
     */
    public function validate(SplFileInfoWrapper $splFileInfo): void
    {
        $importFile = $splFileInfo->openFile(mode: 'rb');
        $lineCount = 0;
        while (!$importFile->eof()) {
            $line = $importFile->fgets();
            if (preg_match('/[0-9a-zA-Z]/', $line)) {
                $lineCount += 1;
            }
        }

        if ($lineCount < 30) {
            throw new LessThan30Exception();
        }

        $extension = $splFileInfo->getExtension();
        if ($extension != 'csv') {
            throw new InvalidCsvFormatException();
        }

    }


}