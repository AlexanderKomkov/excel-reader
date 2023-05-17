<?php

namespace App\SharedData;

use App\Contracts\SharedData;

class TxtData implements SharedData
{
    public function get(int $index): string {
        return '';
    }

    public function save(string $data, int $index): bool
    {
        return true;
    }
}