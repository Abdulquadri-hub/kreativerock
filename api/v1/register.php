<?php

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/ExternalSmsApi.php';
require_once $rootFolder . 'utils/sanitize.php';

$externalSmsApi = new ExternalSmsApi();

$externalSmsApi->generateAPIKeys();