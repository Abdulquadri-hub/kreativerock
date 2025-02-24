<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$campaign = new Campaign();

try {
    $_REQUEST['email'] = $_SESSION["elfuseremail"] ??  null;
    $result = $campaign->getCampaign($_REQUEST);
    if(!$result){
       echo json_encode(['status' => false, 'message' => 'Campaigns not found.', 'data' => []]); 
       exit;
    }
    
    echo json_encode(['status' => true, 'message' => 'Campaigns fetched successfully.', 'data' => $result]);
    
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}