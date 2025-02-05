<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$res = $user->getUserInfo("email = '" . $_SESSION["elfuseremail"] . "'");

$link = $_SERVER['DOCUMENT_ROOT'] .'/elfrique/referral?ref=';
if($res){
    $referral = $res["referralcode"];
    $link = $link . $referral;
    
    $response = array(
        "status" => true,
        "code" => 200,
        "link" => $link,
        "message" => "Successful"
        );
    echo json_encode($response);
}else{
	echo badRequest(204,'Not successful');
}
?>
