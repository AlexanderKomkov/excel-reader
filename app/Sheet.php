<?php

namespace App;

use ZipArchive;
use Exception;
use Closure;
use XMLParser;

class Sheet
{
    private ZipArchive $zip;

    private SharedStrings $sharedString;

    private string $filename;

    protected XMLParser $parser;

    protected string $currentTag;

    protected array $currentAttribs;

    protected array $currentRow;

    protected array $currentCellAttribs;

    protected string $currentContent;

    protected array $rows;

    public function __construct(ZipArchive $zip, SharedStrings $sharedString, string $filename)
    {
        $this->zip = $zip;
        $this->sharedString = $sharedString;
        $this->filename = $filename;
    }

    public function reading(Closure $callback): void 
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

            if (!empty($this->rows)) {
                foreach ($this->rows as $row => $cells) {
                    $callback($row, $cells);
                }
            }
        }

        xml_parser_free($this->parser);
        fclose($stream);
    }

    protected function startTag($parser, $name, $attribs)
    {
        $this->currentTag = $name;
        $this->currentAttribs = $attribs;

        switch ($name) {
            case 'ROW':
                $this->currentRow['attribs'] = $attribs;
                break;
            case 'C':
                $this->currentCellAttribs = $attribs;
                break;
            case 'V':
                $this->currentContent = '';
                break;
        }
    }

    protected function endTag($parser, $name)
    {
        $this->currentTag = '';
        $this->currentAttribs = [];

        switch ($name) {
            case 'ROW':
                $this->saveRow();
                $this->currentRow = [];
                break;
            case 'C':
                $this->currentCellAttribs = [];
                break;
            case 'V':
                $this->currentRow['cells'][] = [
                    'content' => $this->currentContent,
                    'attribs' => $this->currentCellAttribs
                ];
                break;
        }
    }

    protected function contents($parser, $data)
    {
        if ($this->currentTag == 'V') {
            $this->currentContent .= $data;
        }
    }

    private function saveRow(): void 
    {
        $index = $this->currentRow['attribs']['R'];
        $row = [];

        if (isset($this->currentRow['cells'])) {
            foreach($this->currentRow['cells'] as $cell) {
                $t = isset($cell['attribs']['T']) ? $cell['attribs']['T'] : false;
                $r = isset($cell['attribs']['R']) ? $cell['attribs']['R'] : false;
                $symCell = (!empty($r)) ? str_replace($index, '', $r) : false;

                if (!empty($t) && $t == 's') {
                    $indexContent = (int) $cell['content'];
                    $value = $this->sharedString->getContentOnIndex($indexContent);
                } else {
                    $value = trim($cell['content']);
                }

                $row[$symCell] = $value;
            }
        }

        $this->rows[$index] = $row;
    }
}