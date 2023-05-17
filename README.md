# Excel-reader

Реализация небольшого читателя excel файлов в формате .xlsx, без использования фреймворков и готовых пакетов, с использованием ООП

## Установка и запуск локально

1. Создаем рабочую директорию и переходим в нее `mkdir ${app} && cd ${app}`
2. Клонируем репозиторий `git clone https://github.com/AlexanderKomkov/excel-reader.git ./`
3. Выполняем `composer install`
4. Запускаем докер `docker-compose up -d`

## Как использовать

```php
$priceList = dirname(__FILE__) . '/pricelists/price.xlsx';

$excelReader->open($priceList);

foreach($excelReader->getSheets() as $sheet) {
    $sheet->reading(function($row, $cells) {
        echo '<pre>' . print_r($cells, true) . '</pre>';
        if ($row > 4) die();
    });
}

$excelReader->close();
```

## Результат print_r

```
Array
(
    [A] => MB
    [B] => 000000000530
    [C] => MB000000 000530_болт! М4.8\ MB
    [D] => 000000000530_MB
    [E] => 000000 000530
    [F] => 4
    [G] => 134.02
)
Array
(
    [A] => MB
    [B] => 000000001410
    [C] => MB000000001410_болт крепления защиты картера двигателя М8!\ MB
    [D] => 000000001410_MB
    [E] => 000000001410
    [F] => 5
    [G] => 188.44
)
Array
(
    [A] => MB
    [B] => 000000002102
    [C] => MB000000 002102_штифт успокоителя цепи ГРМ!\ MB
    [D] => 000000002102_MB
    [E] => 000000 002102
    [F] => 4
    [G] => 709.49
)
Array
(
    [A] => MB
    [B] => 000000004138
    [C] => MB000000004138_болт! Torx, М10\ MB
    [D] => 000000004138_MB
    [E] => 000000004138
    [F] => 5
    [G] => 1144.99
)
Array
(
    [A] => UAZ
    [B] => 000000035330097
    [C] => 000000035330097_шпилька специальная! М10х28\ УАЗ
    [D] => 00000003533009_UAZ
    [E] => 000000035330097
    [F] => 1
    [G] => 116.50
)
```