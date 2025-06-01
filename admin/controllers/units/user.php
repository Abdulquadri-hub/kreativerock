<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$smsIntegration = new SmsIntegration();

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