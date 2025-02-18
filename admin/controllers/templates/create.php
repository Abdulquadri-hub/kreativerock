<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";


// if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
// }

$template = new Template();

$jsonInput = file_get_contents('php://input');
$templateData = json_decode($jsonInput, true);

$email = $_SESSION["elfuseremail"] ?? "abdulquadri.aq@gmail.com";

$result = $template->create($templateData, $email);
if(!$result['status']){
    exit(error($result));
}
else {
    echo success($result);
}
