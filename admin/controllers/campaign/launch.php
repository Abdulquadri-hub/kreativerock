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

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$campaign = new Campaign();

$email = $_SESSION["elfuseremail"] ?? null;
$campaignId = isset($_REQUEST['campaign_id']) && $_REQUEST['campaign_id'] !== "" ? $_REQUEST['campaign_id'] : "";

if($campaignId === ""){
    echo json_encode(['status' => false, 'message' => 'Campaign id is required.']);
    exit;
}

$result = $campaign->launchCampaign($campaignId, $email); 
echo json_encode($result);

