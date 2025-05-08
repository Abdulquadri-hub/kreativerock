<?php
session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}
   
$facebook = new Facebook();

if (isset($_GET['state'])) {
    $_SESSION['FBRLH_state'] = $_GET['state'];
}
   
$result = $facebook->handleCallback();
echo json_encode($result);