<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/csv_data.php');

const STOP_STATISTICS = true;
const NO_KEEP_STATISTIC = true;
const NOT_CHECK_PERMISSIONS = true;
const NO_AGENT_CHECK = true;
const LID = "s1";
set_time_limit(0);
ini_set('display_errors', 1);

const COMPANIES_IBLOCK_CODE = 'companies_from_csv';
const CSV_FILE = 'companies.csv';


