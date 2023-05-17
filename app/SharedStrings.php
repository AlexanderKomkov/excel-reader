<?php

namespace App;

use Exception;
use ZipArchive;
use XMLParser;

use App\Contracts\SharedData;

class SharedStrings
{
    private ZipArchive $zip;

    private SharedData $sharedData;

    private string $filename;

    private XMLParser $parser;

    private string $currentContent;

    private string $currentTag;

    private int $index = 0;

    public array $sharedStringData = [];

    public function __construct(ZipArchive $zip, SharedData $sharedData, string $filename)
    {
        $this->zip = $zip;
        $this->sharedData = $sharedData;
        $this->filename = $filename;
    }

    public function save(): bool
    {
        $stream = $this->zip->getStream($this->filename);
        if (!is_resource($stream)) throw new Exception('Stream is not resource');

        $this->parser = xml_parser_create('UTF-8');
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "startTag", "endTag");
        xml_set_character_data_handler($this->parser, "contents");

        while (!feof($stream))
        {
            $data = fread($stream, 4096);
            xml_parse($this->parser, $data, feof($stream));
        }

        xml_parser_free($this->parser);
        fclose($stream);

        return true;
    }

    protected function startTag($parser, $name, $attribs)
    {
        $this->currentTag = $name;

        if ($name == 'T') {
            $this->currentContent = '';
        }
    }

    protected function endTag($parser, $name)
    {
        $this->currentTag = '';

        if ($name == 'T') {
            $this->saveContent();
            $this->index++;
        }	
    }

    private function contents($parser, $data)
    {
        if ($this->currentTag == 'T') {
            $this->currentContent .= $data;   
        }
    }

    private function saveContent(): void 
    {
        $content = trim($this->currentContent);
        $index = $this->index;

        $this->sharedData->save($content, $index);
    }

    public function getContentOnIndex(int $index): string 
    {
        return $this->sharedData->get($index);
    }

}