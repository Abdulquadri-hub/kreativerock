<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

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
    
    $stats = $smsIntegration->getDashboardStats($userEmail);

    echo json_encode([
        'status' => true,
        'message' => 'Dashboard statistics fetched successfully.',
        'data' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}