<?php

namespace App\Repository;

use App\Service\SplFileInfoWrapper;
use Symfony\Component\Filesystem\Filesystem;

class ExchangeRepository implements \JsonSerializable
{
    private array $exchanges = [];
    private const FILE_DIR = 'var/import.csv';
    private Filesystem $filesystem;
    private SplFileInfoWrapper $splFileInfo;

    public function __construct(Filesystem $filesystem, SplFileInfoWrapper $splFileInfo)
    {
        $this->filesystem = $filesystem;
        $this->splFileInfo = $splFileInfo;
    }



    public function getDataFromFile(): array
    {
        $dataPoints=[];
        $lineCount=0;
        if ($this->filesystem->exists(self::FILE_DIR) !== false) {
            $importFile = $this->splFileInfo->openFile(mode: 'rb');



            while (!$importFile->eof()) {
                $lineCount+=1;
                $importFile->fgets();
            }

            $importFile->rewind();
            $startLine = rand(0,max(0,$lineCount-30));

            // Move the file pointer to the startLine
            for ($i = 0; $i < $startLine; $i++) {
                $importFile->fgets();
            }

            // Read and store 30 consecutive lines (data points)
            for ($i = 0; $i < 30; $i++) {
                if ($importFile->eof()) {
                    break; // Exit loop if end of file is reached
                }
                $row = $importFile->fgets();
                $dataPoints[] = $row;
            }
        }
        return $dataPoints;
    }

    public function jsonSerialize(): array
    {
        return $this->exchanges;
    }


    public function getAllExchanges():array
    {
        return $this->jsonSerialize();
    }
}