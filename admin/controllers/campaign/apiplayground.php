<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$campaign = new Campaign();

$jsonInput = file_get_contents('php://input');
$params = json_decode($jsonInput, true);

$email = $_SESSION["elfuseremail"] ?? null;

if (json_last_error() !== JSON_ERROR_NONE) {
    exit(badRequest(204,'Invalid JSON data'));
}


$recipient = $params['recipient'];
$message = $params['message'];

$result = $campaign->apiPlayground($recipient, $message, $email);
echo json_encode($result);