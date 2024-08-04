<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\PropertyTable;

class TnTestCompaniesListComponent extends CBitrixComponent
{

	const GRID_ID = 'TEST_LIST';

	public static array $fields = [];

	public static array $properties = [];

	public function executeComponent(): void
	{
		$fields = $this->arParams['LIST_FIELD_CODE'];
		$properties = $this->arParams['LIST_PROPERTY_CODE'];

		self::$fields = array_filter($fields, fn($value) => $value !== '');
		if (!array_key_exists('ID', self::$fields))
		{
			self::$fields[] = 'ID';
		}
		self::$properties = array_filter($properties, fn($value) => $value !== '');

		$fieldsAndProperties = array_merge(self::$fields, self::$properties);

		$names = self::getNames();

		$gridHeaders = self::prepareHeaders($names);

		$gridFilterFields = self::prepareFilterFields($fieldsAndProperties, $names);

		$gridSortValues = self::prepareSortParams($fieldsAndProperties);

		$gridFilterValues = self::prepareFilterParams($gridFilterFields, $fieldsAndProperties);

		$params = self::prepareQueryParams($gridFilterValues, $gridSortValues);


		$companies = self::getCompanies($params);

		$rows = self::getRows($companies, $this->arParams, $fieldsAndProperties);

		$this->arResult = [
			'COMPANIES' => $companies,
			'GRID_ID' => self::GRID_ID,
			'HEADERS' => $gridHeaders,
			'ROWS' => $rows,
			'SORT' => $gridSortValues,
			'FILTER' => $gridFilterFields,
			'ENABLE_LIVE_SEARCH' => false,
			'DISABLE_SEARCH' => true,
		];

		$this->IncludeComponentTemplate();
	}

	private function getCompanies(array $params): array
	{
		$iblock = \Bitrix\Iblock\Iblock::wakeUp($this->arParams['IBLOCK_ID'])->getEntityDataClass();

		$result = $iblock::query()
			->setSelect($params['select'])
			->setOrder($params['sort'])
			->setFilter($params['filter'])
			->exec();

		$arCompanies = [];

		foreach ($result as $key => $item)
		{
			foreach (self::$fields as $field)
			{
				$arCompanies[$key][$field] = $item[$field];
			}
			foreach (self::$properties as $property)
			{
				$arCompanies[$key][$property] = $item[$property . '_VALUE'];
			}
		}

		return $arCompanies;
	}

	private static function getRows(array $companies, array $arParams, array $fieldsAndProperties): array
	{
		$rows = [];

		foreach ($companies as $key => $company)
		{
			$viewUrl = CComponentEngine::makePathFromTemplate(
				$arParams['URL_TEMPLATES']['DETAIL'],
				['COMPANY_ID' => $company['ID']]
			);

			$rows[] = [
				'id' => $company['ID'],
				'data' => $company,
			];

			foreach ($fieldsAndProperties as $column)
			{
				if ($column === 'NAME')
				{
					$value = '<a href="' . htmlspecialcharsEx($viewUrl) . '" target="_self">' . $company['NAME'] . '</a>';
				} else
				{
					$value = $company[$column];
				}
				$rows[$key]['columns'][$column] = $value;
			}
		}

		return $rows;
	}

	private static function prepareProperties(array $props): array
	{
		if (empty($props))
			return $props;

		$result = [];

		foreach ($props as $key => $value)
		{
			if (in_array($key, self::$properties))
			{
				$result[$key . '_VALUE'] = $value;
			} else
			{
				$result[$key] = $value;
			}
		}
		return $result;
	}

	private static function prepareSelectParams(): array
	{
		$result = [];

		foreach (self::$properties as $property)
		{
			$result[$property . '_VALUE'] = $property . '.VALUE';
		}

		return array_merge($result, self::$fields);
	}

	private static function prepareQueryParams(array $gridFilterValues, array $gridSortValues): array
	{
		return [
			'select' => self::prepareSelectParams(),
			'filter' => self::prepareProperties($gridFilterValues),
			'sort' => self::prepareProperties($gridSortValues),
		];
	}

	private static function prepareSortParams(array $fieldsAndProperties): array
	{
		$grid = new Bitrix\Main\Grid\Options(self::GRID_ID);

		$gridSortValues = $grid->getSorting();

		$gridSortValues = array_filter(
			$gridSortValues['sort'],
			function ($field) use ($fieldsAndProperties) {
				return in_array($field, $fieldsAndProperties);
			},
			ARRAY_FILTER_USE_KEY
		);

		if (empty($gridSortValues))
		{
			$gridSortValues = ['ID' => 'asc'];
		}

		return $gridSortValues;
	}

	private static function prepareFilterParams($filterFields, $fieldsAndProperties): array
	{
		$gridFilter = new Bitrix\Main\UI\Filter\Options(self::GRID_ID);
		$gridFilterValues = $gridFilter->getFilter($filterFields);

		return array_filter(
			$gridFilterValues,
			function ($fieldName) use ($fieldsAndProperties) {
				return in_array($fieldName, $fieldsAndProperties);
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	private static function prepareHeaders($names): array
	{
		$headers = [];


		foreach ($names as $field => $name)
		{
			$headers[] = [
				'id' => $field,
				'name' => $name,
				'sort' => $field,
				'first_order' => 'desc',
				'default' => true,
			];
		}

		return $headers;
	}

	private static function prepareFilterFields(array $fieldsAndProperties, array $names): array
	{
		$filterFields = [];

		foreach ($fieldsAndProperties as $field)
		{
			if (!empty($field))
			{
				$filterFields[] = [
					'id' => $field,
					'name' => $names[$field],
					'sort' => $field,
					'first_order' => 'desc',
					'default' => true,
				];
			}
		}

		return $filterFields;
	}

	private static function getFieldNames(): array
	{
		$names = [];
		foreach (self::$fields as $field)
		{
			$names[$field] = Loc::getMessage('IBLOCK_FIELD_' . $field);
		}
		return $names;
	}
	private static function getPropertiesNames(): array
	{
		$names = [];
		$result = PropertyTable::query()
			->setSelect(['NAME', 'CODE'])
			->setFilter(['CODE' => self::$properties ])
			->exec();

		foreach ($result as $item)
		{
			$names[$item['CODE']] = $item['NAME'];
		}
		return $names;
	}

	private static function getNames(): array
	{
		$fieldNames = self::getFieldNames();
		$propertiesNames = self::getPropertiesNames();
		return array_merge($fieldNames, $propertiesNames);
	}
}
