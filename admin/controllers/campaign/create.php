<?php

session_start();

header('Content-Type: application/json');

$rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/Campaign.php';
require_once $rootFolder . 'utils/sanitize.php';

// if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
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
