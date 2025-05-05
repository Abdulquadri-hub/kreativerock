<?php
session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

// if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
// }

unset($_SESSION['FBRLH_state']);
   
$facebook = new Facebook();

$redirectUrl = "https://comeandsee.com.ng/kreativerock/admin/controllers/facebook/callback";
$loginUrl = $facebook->getLoginUrl($redirectUrl);
echo json_encode(['url' => $loginUrl]);