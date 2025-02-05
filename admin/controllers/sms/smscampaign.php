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
require_once $rootFolder . 'model/SmsCampaign.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}


$user = new User();
$smsCampaign = new SmsCampaign();

$jsonInput = file_get_contents('php://input');
$campaignParams = json_decode($jsonInput, true);

$email = $_SESSION["elfuseremail"] ??  null;

if (json_last_error() !== JSON_ERROR_NONE) {
    exit(badRequest(204,'Invalid JSON data'));
}

try {
    
    $result = $smsCampaign->createCampaign($campaignParams, $email); 
        
    if ($result['status']) {

        echo json_encode([
            'status' => true,
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'campaign_id' => $result['campaign_id'] ?? null
        ]);
    } else {
            
        echo json_encode([
            'status' => false,
            'message' => $result['message'],
            'errors' => $result['errors'] ?? null
        ]);
    }
        

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'An error occurred while processing the campaign',
        'error' => $e->getMessage()
    ]);
}
    