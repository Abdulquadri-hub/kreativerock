<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$campaign = new Campaign();

$jsonInput = file_get_contents('php://input');
$campaignParams = json_decode($jsonInput, true);
$email = $_SESSION["elfuseremail"] ?? null;

if (json_last_error() !== JSON_ERROR_NONE) {
    exit(badRequest(204,'Invalid JSON data'));
}

$result = $campaign->updateCampaign($campaignParams, $email); 

if (isset($result['status']) && $result['status']) {
    echo json_encode([
        'status' => true,
        'message' => $result['message'],
        'data' => $result['data'] ?? null,
        'campaign_id' => $result['campaign_id'] ?? null
    ]);
} else {
        
    echo json_encode([
        'status' => $result['status'],
        'code' => $result['code'],
        'message' => $result['message'],
        'errors' => $result['errors'] ?? null
    ]);
}
