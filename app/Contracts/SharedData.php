<?php

namespace App\Contracts;

interface SharedData
{
    public function get(int $index): string;

    public function save(string $data, int $index): bool;
}