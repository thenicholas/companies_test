<?php

use Bitrix\Main\Engine\CurrentUser;

require_once 'config.php';
require_once 'Tools.php';

if (CurrentUser::get()->isAdmin())
{
	define('COMPANIES_IBLOCK_ID', Tools::getCompaniesIblockId());

	$csvFile = Tools::readCSVFile(CSV_FILE);

	$fields = [
		'name' => 0,
		'fioRepresentative' => 1,
		'phone' => 2,
		'email' => 3,
		'position' => 4,
		'description' => 5,
	];



	while ($company = $csvFile->Fetch())
	{

		foreach ($fields as $varName => $index)
		{
			${$varName} = htmlspecialcharsEx(!empty($company[$index]) ? $company[$index] : '');
		}

		$arProperties = [
			'FIO_REPRESENTATIVE' => $fioRepresentative,
			'PHONE' => $phone,
			'EMAIL' => $email,
			'POSITION' => $position,
		];

		$arFields = [
			'MODIFIED_BY' => $USER->GetID(),
			'IBLOCK_SECTION_ID' => false,
			'IBLOCK_ID' => COMPANIES_IBLOCK_ID,
			'NAME' => $name,
			'DETAIL_TEXT' => $description,
			'ACTIVE' => 'Y',
			'PROPERTY_VALUES' => $arProperties
		];

		Tools::addCompany($arFields);
	}

	$csvFile->CloseFile();

	Tools::logMessage('================');
	Tools::logMessage('Количество добавленных компаний: ' . Tools::$successCounter);
	Tools::logMessage('Количество ошибок: ' . Tools::$errorCounter);

}

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_after.php');