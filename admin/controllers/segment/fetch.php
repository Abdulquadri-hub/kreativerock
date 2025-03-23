<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}


$user = new User();
$segment = new Segment();

$email = $_SESSION["elfuseremail"] ??  null;
$request = $_REQUEST;

$request['email'] = $email;

$result =  $segment->getSegments($request);
echo json_encode($result); 

