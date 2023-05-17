<?php

namespace App\SharedData;

use App\Contracts\SharedData;

class PropertyData implements SharedData
{
    private $data = [];

    public function get(int $index): string {
        return array_key_exists($index, $this->data) ? $this->data[$index] : '';
    }

    public function save(string $data, int $index): bool
    {
        $this->data[$index] = $data;
        return isset($this->data[$index]);
    }
}