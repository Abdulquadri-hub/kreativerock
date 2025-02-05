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
require_once $rootFolder . 'model/ContactImporter.php';
require_once $rootFolder . 'model/SmsPurchase.php';
require_once $rootFolder . 'model/SmsIntegration.php';
require_once $rootFolder . 'model/Conversation.php';
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'utils/sanitize.php';

$user = new User();
$smsCampaign = new SmsCampaign();
$twoWaySms = new TwoWaySms();
$smsPurchase = new SmsPurchase();
$smsIntegration = new SmsIntegration();
$conversation = new Conversation();
$contactImporter = new ContactImporter();

$textInput = 
"EMAIL,FIRSTNAME,LASTNAME,PHONE
abdulquadri.aq@gmail.com,Abdul,Quadri,09076189518
";
echo json_encode($contactImporter->importFromText($textInput));





