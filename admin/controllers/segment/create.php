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
require_once $rootFolder . 'model/Segment.php';
require_once $rootFolder . 'model/Contact.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$segment = new Segment();

$email = $_SESSION["elfuseremail"] ??  null;
$name = isset($_REQUEST['name']) && $_REQUEST['name'] !== "" ? strip_tags($_REQUEST['name']) : "";
$description = isset($_REQUEST['description']) && $_REQUEST['description'] !== "" ? strip_tags($_REQUEST['description']) : "";

$result =  $segment->create([
    'name' => $name,
    'description' => $description,
], $email);
echo json_encode($result); 

