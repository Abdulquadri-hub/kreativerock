<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$gushupApi = new GupshupAPI();

$email = "abdulquadri.aq@gmail.com";

$result = $gushupApi->getAppToken();
echo json_encode($result);
