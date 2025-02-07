<?php

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
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'utils/sanitize.php';

$user = new User();
$campaign = new Campaign();
$twoWaySms = new TwoWaySms();
$smsPurchase = new SmsPurchase();
$errors = [];

if($twoWaySms->validate($_POST)){

    $senderId = $_POST['user_id'] ?? null;
    $destinations = $_POST['destinations'] ? explode(",", $_POST['destinations']) : null;
    $message_type = $_POST['message_type'] ?? null;
    $message = $_POST['message'];
    $interactionType = $_POST['interaction_type'] ?? null;
    
    $data = $twoWaySms->sendMessage($senderId, null, null, $destinations, $message_type, 'outgoing', $message, null, null, $interactionType);
    echo json_encode($data); 

}else{
    $errors = $twoWaySms->errors;
    echo json_encode(['status' => false, 'errors' => $errors]);
}



