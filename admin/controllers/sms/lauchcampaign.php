<?php

session_start();

header('Content-Type: application/json');

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/SmsPackage.php';
require_once $rootFolder . 'model/Campaign.php';
require_once $rootFolder . 'model/SmsPurchase.php';
require_once $rootFolder . 'model/SmsIntegration.php';
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$campaign = new Campaign();
$errors = [];

$postData = $_REQUEST;
if (!isset($postData['campaign_id']) || empty($postData['campaign_id'])) {
    $errors['campaign_id'] = "Campaign id is required.";
    echo json_encode(['status' => false, 'errors' => $errors]);
    exit;
} else {
   $campaignId = $postData['campaign_id'];
}

$email = $_SESSION["elfuseremail"] ??  null;
$result = $campaign->launchCampaign($campaignId, $email);
if($result){
    echo json_encode($result); 
}else{
    echo json_encode($result); 
}

