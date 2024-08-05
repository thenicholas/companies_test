<?php

$urlTemplates = [
    'DETAIL' => $arParams['SEF_FOLDER'] . $arParams['SEF_URL_TEMPLATES']['detail'],
];

$APPLICATION->IncludeComponent(
    'nicholas.test:companies.detail',
    '',
    [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'COMPANY_ID' => $arResult['VARIABLE_ALIASES']['COMPANY_ID'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'URL_TEMPLATES' => $urlTemplates,
        'SEF_FOLDER' => $arResult['SEF_FOLDER'],
        'DETAIL_FIELD_CODE' => $arParams['DETAIL_FIELD_CODE'],
        'DETAIL_PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
    ],
    $this->getComponent(),
    [
        'HIDE_ICONS' => 'N',
    ]
);
