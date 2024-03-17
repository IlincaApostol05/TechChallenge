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



    public function getDataFromFile(): void
    {
        if ($this->filesystem->exists(self::FILE_DIR) !== false) {
            $importFile = $this->splFileInfo->openFile(mode: 'rb');
            while (!$importFile->eof()) {
                $row = $importFile->fgets();
                echo $row;
            }
        }
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