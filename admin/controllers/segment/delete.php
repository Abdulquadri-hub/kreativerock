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
$segment_id = isset($_REQUEST['segment_id']) && $_REQUEST['segment_id'] !== "" ? strip_tags($_REQUEST['segment_id']) : "";

$result =  $segment->delete($segment_id, $email);
echo json_encode($result); 

