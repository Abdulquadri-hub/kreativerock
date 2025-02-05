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
require_once $rootFolder . 'model/SmsCampaign.php';
require_once $rootFolder . 'model/SmsPurchase.php';
require_once $rootFolder . 'model/SmsIntegration.php';
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$smsCampaign = new SmsCampaign();
$errors = [];

$postData = $_REQUEST;

if (!isset($postData['campaign_id']) || empty($postData['campaign_id'])) {
    $errors['campaign_id'] = "Campaign id is required.";
    echo json_encode(['status' => false, 'errors' => $errors]);
    exit;
} else {
   $campaignId = $postData['campaign_id'];
}

if($smsCampaign->validate($postData)){
    $validated = $smsCampaign->getValidatedData();
    
    $email = $_SESSION["elfuseremail"] ??  null;
    $campaignName = $validated['campaignname'];
    $phoneNumbers = $validated['contacts'];
    $smsMessage = $validated['campaignmessage'];
    $scheduleDate = $validated['scheduledate'];
    $repeatInterval = $validated['repeatinterval'];
    $smsPages = ceil(strlen($smsMessage) / 160);
    $type = $validated['campaigntype'];
    $responseHandling = $validated['responsehandling'];
    $submitaction = $validated['submitaction'];
    
    $prompts = [];

    $promptRows = $validated['promptsrows'];

    for ($i = 0; $i < $promptRows; $i++) {

        $prompt = isset($validated["prompt{$i}"]) ? $validated["prompt{$i}"] : null;
        $expectedResponse = isset($validated["expectedresponse{$i}"]) ? $validated["expectedresponse{$i}"] : null;
        $response = isset($validated["response{$i}"]) ? $validated["response{$i}"] : null;
        $expectedResponsetype = isset($validated["expectedResponsetype{$i}"]) ? $validated["expectedResponsetype{$i}"] : null;

        $prompts[] = [
            'prompt' => $prompt,
            'expectedResponse' => $expectedResponse,
            'response' => $response,
            'expectedResponsetype' => $expectedResponsetype,
        ];
    }
    
    $result = $smsCampaign->updateCampaign(
        $email,
        $campaignId, 
        $campaignName, 
        $phoneNumbers, 
        $smsMessage, 
        $scheduleDate, 
        $repeatInterval, 
        $smsPages, 
        $type, 
        $responseHandling, 
        $prompts, 
        $submitaction
    );
    echo json_encode($result); 
}else{
    echo json_encode(['status' => false, 'errors' => $smsCampaign->validationErrors()]);
}










