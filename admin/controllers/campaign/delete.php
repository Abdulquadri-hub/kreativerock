<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$campaign = new Campaign();

$campaignId = isset($_REQUEST['campaign_id']) && $_REQUEST['campaign_id'] !== "" ? $_REQUEST['campaign_id'] : "";
$email = $_SESSION["elfuseremail"] ??  null;

if($campaignId === ""){
    echo json_encode(['status' => false, 'message' => 'Campaign id is required.']);
    exit;
}

$results =  $campaign->deleteCampaign($campaignId, $email);
if(!empty($results)){
    echo json_encode($results);
}else{
    echo json_encode($results);
}



