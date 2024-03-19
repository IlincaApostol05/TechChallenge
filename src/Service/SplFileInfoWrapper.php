<?php

namespace App\Service;

use SplFileObject;
use SplFileInfo;

class SplFileInfoWrapper
{

    private SplFileInfo $splFileInfo;

    public function __construct(string $path)
    {
        $this->splFileInfo = new SplFileInfo($path);
    }

    public function openFile(string $mode = "r"): SplFileObject
    {
        return $this->splFileInfo->openFile($mode);

    }

    public function getExtension(): string
    {
        return $this->splFileInfo->getExtension();

    }
}
