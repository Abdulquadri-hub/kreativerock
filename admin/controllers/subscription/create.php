<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

// if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
// }


$gushup = new GupshupAPI();

$jsonInput = file_get_contents('php://input');
$subscriptionData = json_decode($jsonInput, true);

$appId = $subscriptionData['appid'] ?? $gushup->getCurrentAppId();
$result = $gushup->setSubscription($appId, $subscriptionData);
echo json_encode($result);
