<?php

namespace App;

use Exception;
use ZipArchive;
use Closure;

class ExcelReader
{
    private ExcelFile $excelFile;

    private ZipArchive $zip;

    private bool $isOpenZip;

    private array $zipFales;

    private SharedStrings $sharedString;

    private array $sheets;

    public function open(string $excelFile): ExcelReader
    {
        $this->excelFile = new ExcelFile($excelFile);
        $this->zip = new ZipArchive;

        if ($this->zip->open($excelFile)) {
            $this->isOpenZip = true;
            $this->saveZipFiles();
            $this->setSharedStringsAndSheets();
            $this->clearZipFiles();

            $this->sharedString->save();

        }

        return $this;
    }

    private function setSharedStringsAndSheets(): void 
    {
        $filenameSharedString = $this->getFilenameSharedString();
        $filenamesSheet = $this->getFilenamesSheet();

        $this->sharedString = new SharedStrings($this->zip, $filenameSharedString);

        foreach($filenamesSheet as $filenameSheet) {
            $this->sheets[] = new Sheet($this->zip, $this->sharedString, $filenameSheet);
        }
    }

    private function saveZipFiles(): void 
    {
        $this->clearZipFiles();

        for ($i = 0; $i < $this->zip->numFiles; $i++)
        {
            $this->zipFales[] = $this->zip->getNameIndex($i);
        } 
    }

    private function clearZipFiles(): void 
    {
        $this->zipFales = [];
    }

    private function getFilenameSharedString(): string 
    {
        foreach ($this->zipFales as $filename) {
            if ($filename == 'xl/sharedStrings.xml') {
                return $filename;
            }
        }

        throw new Exception('File xl/sharedStrings.xml not found');
    }

    private function getFilenamesSheet(): array 
    {
        $sheets = [];

        foreach ($this->zipFales as $filename) {
            if (str_starts_with($filename, 'xl/worksheets/')) {
                $sheets[] = $filename;
            }
        }

        if (empty($sheets)) {
            throw new Exception('Files xl/worksheets/* not found');
        }

        sort($sheets);
        return $sheets;
    }

    public function getSheets(): array 
    {
        if (empty($this->sheets)) throw new Exception('Empty sheets');
        return $this->sheets;
    }

    public function close(): bool
    {
        if ($this->isOpenZip && $this->zip->close()) {
            $this->isOpenZip = false;
            return true;
        }
        return false;
    }
}