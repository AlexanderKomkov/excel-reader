<?php

interface SharedData
{
    public function get(int $index): string;

    public function save(string $data): bool;
}