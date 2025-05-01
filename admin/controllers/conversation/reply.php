<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$email = $_SESSION["elfuseremail"] ??  null;
$twoWaySms = new TwoWaySms();

$data = $_REQUEST;
$data['email'] = $email;

// $results =  $twoWaySms->sendReply($data);
echo json_encode($results);





