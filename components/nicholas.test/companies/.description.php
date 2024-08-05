<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentDescription = [
    'NAME' => 'Компании, тестовое задание',
    'DESCRIPTION' => 'Формирует список компаний',
    //'ICON' => '/images/eaddlist.gif',
    'CACHE_PATH' => 'Y',
    'SORT' => 30,
    'COMPLEX' => 'Y',
    'PATH' => [
        'ID' => 'Тестовое задание',
        'CHILD' => [
            'ID' => 'test_company',
            'NAME' => 'Компании тестовое задание',
        ],
    ],
];