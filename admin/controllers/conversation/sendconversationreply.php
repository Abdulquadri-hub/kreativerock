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
require_once $rootFolder . 'model/Conversation.php';
require_once $rootFolder . 'model/TwoWaySms.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'utils/sanitize.php';


if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$email = $_SESSION["elfuseremail"] ??  null;
$twoWaySms = new TwoWaySms();

$data = $_REQUEST;
$data['email'] = $email;
$results =  $twoWaySms->sendReply($data);
echo json_encode($results);





