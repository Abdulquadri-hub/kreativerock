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
$name = isset($_REQUEST['name']) && $_REQUEST['name'] !== "" ? strip_tags($_REQUEST['name']) : "";
$description = isset($_REQUEST['description']) && $_REQUEST['description'] !== "" ? strip_tags($_REQUEST['description']) : "";
$segment_id = isset($_REQUEST['segment_id']) && $_REQUEST['segment_id'] !== "" ? strip_tags($_REQUEST['segment_id']) : "";

$result =  $segment->edit([
    'name' => $name,
    'description' => $description,
], $email, $segment_id);
echo json_encode($result); 

