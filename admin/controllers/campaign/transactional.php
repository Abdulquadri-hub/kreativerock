<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$campaign = new Campaign();
$email = $_SESSION["elfuseremail"] ??  null;

$result = $campaign->getUserTransactionalDetails($email);
echo json_encode($result);