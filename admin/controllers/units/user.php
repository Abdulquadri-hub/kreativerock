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
require_once $rootFolder . 'model/SmsIntegration.php';
require_once $rootFolder . 'utils/sanitize.php';

$user = new User();
$smsIntegration = new SmsIntegration();

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

try {

    $userEmail = $_SESSION["elfuseremail"] ??  null;
    if ($userEmail == null) {
        throw new Exception('User email is required.');
    }
    
    $unitsInfo = $smsIntegration->getUserUnitsInfo($userEmail);

    echo json_encode([
        'status' => true,
        'message' => 'User units infomations fetched successfully.',
        'data' => $unitsInfo
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}