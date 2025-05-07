<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

// if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
// }

$user = new User();
$campaign = new Campaign();

$jsonInput = file_get_contents('php://input');
$campaignParams = json_decode($jsonInput, true);

$email = $_SESSION["elfuseremail"] ?? "abdulquadri.aq@gmail.com";

if (json_last_error() !== JSON_ERROR_NONE) {
    exit(badRequest(204,'Invalid JSON data'));
}
$result = $campaign->createCampaign($campaignParams, $email); 
echo json_encode($result);

