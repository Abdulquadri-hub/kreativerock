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
require_once $rootFolder . 'model/SmsCampaign.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$smsCampaign = new SmsCampaign();

try {
    $_REQUEST['email'] = $_SESSION["elfuseremail"] ??  null;
    $result = $smsCampaign->getCampaign($_REQUEST);
    if(!$result){
       echo json_encode(['status' => false, 'message' => 'Campaigns not found.', 'data' => []]); 
       exit;
    }
    
    echo json_encode(['status' => true, 'message' => 'Campaigns fetched successfully.', 'data' => $result]);
    
} catch (ExceptionType $e) {
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}