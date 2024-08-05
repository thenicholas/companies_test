<?php

use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;

class TnTestCompaniesDetailComponent extends CBitrixComponent
{
    const FORM_ID = 'companies';

    public function executeComponent()
    {
        $fields = $this->arParams['DETAIL_FIELD_CODE'];
        $properties = $this->arParams['DETAIL_PROPERTY_CODE'];
        $fields = array_filter($fields, fn($value) => $value !== '');

        $properties = array_filter($properties, fn($value) => $value !== '');

        $params['select'] = self::prepareSelectParams($fields, $properties);
        $params['filter'] = ['ID' => $this->arParams['COMPANY_ID']];


        if (empty($company)) {
            ShowError(Loc::getMessage('TN_TEST_COMPANIES_NOT_FOUND'));
        }

        if ($this->startResultCache()) {
            $this->SetResultCacheKeys([]);

            $names = self::getPropertyNames($properties, $fields);

            $company = self::getCompany($fields, $properties, $params);

            $this->arResult = $company;
            $this->arResult['NAMES'] = $names;


            $this->includeComponentTemplate();
        }
        global $APPLICATION;
        $APPLICATION->SetTitle(
            Loc::getMessage(
                'TN_TEST_COMPANIES_SHOW_TITLE',
                [
                    '#NAME#' => $company['FIELDS']['NAME'],
                ]
            )
        );
    }

    private static function prepareSelectParams($fields, $properties): array
    {
        $result = [];

        foreach ($properties as $property) {
            $result[$property . '_VALUE'] = $property . '.VALUE';
        }

        return array_merge($result, $fields);
    }

    private function getCompany(array $fields, array $properties, array $params): array
    {
        $iblock = Iblock::wakeUp($this->arParams['IBLOCK_ID'])->getEntityDataClass();

        $result = $iblock::query()
            ->setSelect($params['select'])
            ->setFilter($params['filter'])
            ->exec();

        $company = [];

        foreach ($result as $item) {
            foreach ($fields as $field) {
                $company['FIELDS'][$field] = $item[$field];
            }
            foreach ($properties as $property) {
                $company['PROPERTIES'][$property] = $item[$property . '_VALUE'];
            }
        }
        return $company;
    }

    private static function getPropertyNames(array $properties, array $fields): array
    {
        $names = [];
        $result = PropertyTable::query()
            ->setSelect(['NAME', 'CODE'])
            ->setFilter(['CODE' => $properties])
            ->exec();

        foreach ($result as $item) {
            $names[$item['CODE']] = $item['NAME'];
        }

        foreach ($fields as $field) {
            $names[$field] = Loc::getMessage('IBLOCK_FIELD_' . $field);
        }
        return $names;
    }
}