<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$campaign = new Campaign();

$jsonInput = file_get_contents('php://input');
$params = json_decode($jsonInput, true);

$email = $_SESSION["elfuseremail"] ?? "abdulquadri.aq@gmail.com";

if (json_last_error() !== JSON_ERROR_NONE) {
    exit(badRequest(204,'Invalid JSON data'));
}


$result = $campaign->checkRcsCompatibility($params["phone_numbers"]);
echo json_encode($result);