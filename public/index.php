<?php

require_once('../vendor/autoload.php');

use App\ExcelReader;

$priceList = dirname(__FILE__) . '/pricelists/price.xlsx';

$excelReader = new ExcelReader();

$excelReader->open($priceList);

foreach($excelReader->getSheets() as $sheet) {
    $sheet->reading(function($row, $cells) {
        echo '<pre>' . print_r($cells, true) . '</pre>';
        if ($row > 4) die();
    });
}

$excelReader->close();