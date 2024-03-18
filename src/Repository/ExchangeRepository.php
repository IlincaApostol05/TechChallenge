<?php

namespace App\Repository;

use App\Factory\ExchangeFactory;
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



    public function getDataFromFile(string $filePath): array
    {
        $dataPoints = [];
        $lineCount = 0;

        if ($this->filesystem->exists($filePath)) {
            $importFile = new \SplFileObject($filePath, 'rb');

            while (!$importFile->eof()) {
                $lineCount += 1;
                $line = $importFile->fgets();

                if (!empty($line)) {
                    $exchange = ExchangeFactory::createFromCsvLine($line);
                    if ($exchange !== null) {
                        $dataPoints[] = $exchange;
                    }
                }
            }

            // Adjust this part according to your logic
            $lineCount = count($dataPoints);
            $startLine = rand(0, max(0, $lineCount - 30));
            $importFile->seek($startLine);

            for ($i = 0; $i < 30 && !$importFile->eof(); $i++) {
                $line = $importFile->fgets();
                if (!empty($line)) {
                    $exchange = ExchangeFactory::createFromCsvLine($line);
                    if ($exchange !== null) {
                        $dataPoints[] = $exchange;
                    }
                }
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