<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();


class NicholasTestCompaniesComponent extends CBitrixComponent
{
	const SEF_DEFAULT_TEMPLATES = ['detail' => '#COMPANY_ID#/'];

	public function executeComponent()
	{
		if (!is_array($this->arParams['SEF_URL_TEMPLATES']))
		{
			$this->arParams['SEF_URL_TEMPLATES'] = [];
		}

		$sefTemplates = array_merge(self::SEF_DEFAULT_TEMPLATES, $this->arParams['SEF_URL_TEMPLATES']);

		$page = CComponentEngine::parseComponentPath(
			$this->arParams['SEF_FOLDER'],
			$sefTemplates,
			$arVariables,
		);

		if (empty($page))
		{
			$page = 'list';
		}

		$this->arResult = [
			'SEF_FOLDER' => $this->arParams['SEF_FOLDER'],
			'SEF_URL_TEMPLATES' => $sefTemplates,
			'VARIABLE_ALIASES' => $arVariables,
		];

		$this->IncludeComponentTemplate($page);

	}

}
