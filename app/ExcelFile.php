<?php

namespace App;

use Exception;

class ExcelFile
{
    const ALLOWED_EXTENSION = 'xlsx';

    private string $path;

    private string $dirname;
    
    private string $basename;

    private string $extension;

    private string $filename;

    public function __construct(string $path)
    {
        if (!$this->isValid($path)) throw new Exception('Excel file not valid');

        $this->setPropertiesOnPath($path);
    }

    private function isValid(string $path): bool 
    {
        return file_exists($path) && str_ends_with($path, '.' . static::ALLOWED_EXTENSION);
    }

    private function setPropertiesOnPath(string $path): void 
    {
        $this->path = $path;

        foreach (pathinfo($path) as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function getPath(): string 
    {
        return $this->path;
    }

    public function getDirname(): string 
    {
        return $this->dirname;
    }

    public function getBasename(): string 
    {
        return $this->basename;
    }

    public function getExtension(): string 
    {
        return $this->extension;
    }

    public function getFilename(): string 
    {
        return $this->filename;
    }

}