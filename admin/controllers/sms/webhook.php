<?php

session_start();

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'model/Campaign.php';
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'utils/sanitize.php';

$dotgo = new DotgoApi();
$campaign = new Campaign();
$twoWaySms = new TwoWaySms();

$data = json_decode(file_get_contents('php://input'), true);
if(empty($data)){
    error_log("Error fetching webhook events");
}

switch ($data['event']) {
    case 'message':
        $twoWaySms->handleIncomingMessage($data);
        break;
    case 'isTyping':
          //
          break;     
    case 'messageStatus':
        $twoWaySms->handleMessageStatus($data);
        break;
    case 'response':
        $messageData = $dotgo->handleSuggestedResponse($data);
        break;
    default:
        exit(json_encode(['status'=>false, 'message' => "Unknown event type: {$data['event']}"]));
}






