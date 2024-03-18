<?php

namespace App\Validator;

use App\Exception\FileDoesNotExistException;
use App\Exception\LessThan30Exception;
use App\Service\SplFileInfoWrapper;
use Symfony\Component\Filesystem\Filesystem;

class csvValidator
{
    /**
     * @throws \Exception
     */
    public function validate(string $filePath, Filesystem $filesystem, SplFileInfoWrapper $splFileInfo): void
    {
        if (!($filesystem->exists($filePath))){
            throw new FileDoesNotExistException('FILE DOES NOT EXIST');
        }
        $importFile = $splFileInfo->openFile(mode: 'rb');
        $lineCount=0;
        while (!$importFile->eof()) {
            $line = $importFile->fgets();
            if (preg_match('/[0-9a-zA-Z]/', $line)){
                $lineCount += 1;
            }
        }

        if($lineCount<30){
            throw new LessThan30Exception();
        }

    }

}