<?php

use Bitrix\Iblock\IblockTable;

class Tools
{

    public static int $successCounter = 0;
    public static int $errorCounter = 0;

    public static function getCompaniesIblockId(): int
    {
        try {
            $result = IblockTable::getList([
                'select' => [
                    'ID',
                ],
                'filter' => [
                    '=CODE' => COMPANIES_IBLOCK_CODE,
                ],
                'count_total' => true,
            ]);

            $iblockIdCount = $result->getCount();
            if ($iblockIdCount < 1) {
                die('Не найден ID инфоблока с компаниями, скрипт будет остановлен');
            } elseif ($iblockIdCount > 1) {
                die('Найдено более одного ID инфоблока с компаниями, скрипт будет остановлен');
            } else {
                $companiesIblockId = $result->fetch()['ID'];
                Tools::logMessage("ID инфоблока с компаниями: $companiesIblockId");
                Tools::logMessage('================');
                return $companiesIblockId;
            }
        } catch (Exception $e) {
            die("Ошибка при получении ID инфоблока с компаниями, скрипт будет остановлен: {$e->getMessage()}");
        }
    }

    public static function logMessage(string $message): void
    {
        echo $message . '<br>';
    }

    public static function readCSVFile(string $file): object
    {
        $csvFile = new CCSVData('R', true);
        if (!$csvFile->LoadFile($file)) {
            die("Ошибка чтения CSV файла $file");
        }
        $csvFile->SetDelimiter();

        return $csvFile;
    }

    public static function addCompany(array $arFields): void
    {
        $element = new CIBlockElement;

        if ($newElementId = $element->Add($arFields)) {
            Tools::logMessage("Компания \"{$arFields['NAME']}\" добавлена в инфоблок, ID: $newElementId");
            self::$successCounter++;
        } else {
            Tools::logMessage("Ошибка добавления компании \"{$arFields['NAME']}\" в инфоблок: $element->LAST_ERROR");
            self::$errorCounter++;
        }
    }
}
