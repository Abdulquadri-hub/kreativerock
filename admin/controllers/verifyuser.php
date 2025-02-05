<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
require_once '../../model/smslog.php';
require_once '../../utils/common.php';
require_once '../../utils/sanitize.php';
date_default_timezone_set('Africa/Lagos');

if($_SESSION["elfuseremail"] === null || $_SESSION["elfuseremail"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    //exit(json_encode($response));
    logoff();
}

$user = new User();
//errorhandler("Phone: " . escape($_POST["phone"]));
//errorhandler("Phone: " . $user->getEscapedString(escape($_POST["phone"])));

$smslog = new SMSLog();
$email = $_SESSION["elfuseremail"]; //$user->getEscapedString(escape($_POST["phone"]));
$ver_code = (isset($_GET["vercode"]) && $_GET["vercode"] !== "") ? htmlentities($_OET["vercode"],ENT_QUOTES) : "";
if($ver_code === ""){
    logoff();
    $resp = array("status" => false,"code" => 204,"message" => "Email verification code is invalid");
    //exit(json_encode($resp));
}

$ver_code = base64_decode($ver_code); //$user->getEscapedString(escape($_POST["vercode"]));
//$res = $smslog->getSMSLogVerification($phone);

//$resemail = $smslog->retrieveByQuerySelector("select * from smslogs where accountnumber = '" . $email . "' AND status LIKE 'EMAILVERIFY' ORDER BY id DESC");
$resemail = $smslog->retrieveByQuerySelector("select * from verificationlogs where email = '" . $email . "' AND status LIKE 'EMAILVERIFY' ORDER BY id DESC");
//$ressize = count($res);

if($resemail[0]["verificationcode"] !== $ver_code){
    $resp = array("status" => false,"code" => 204,"message" => "We could not verify the email");
    
    logoff();
    //exit(json_encode($resp));
}
$emailresetres = $smslog->updateVerificationLog("status='EMAILVERIFIED'", $resemail[0]["id"]);
if($emailresetres){
    $user->addUserActivity($email, "Email verification at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "USER VERIFIED");
    $user->updateUserDetails("status = 'VERIFIED'", $_SESSION["user_id"]);
    
    logoff();
    
    //$resp = array("status" => true,"code" => 200,"message" => "Successful");
    //exit(json_encode($resp));
    
}else{
    exit(badRequest(204, "Could not reset log"));
}

function logoff(){
    $_SESSION["elfuseremail"] = null;
    $_SESSION["user_id"] = null;
    $_SESSION["role"] = null;
    //$_SESSION["location_id"] = null;
    session_unset();
    session_destroy();
    
    header('Location: ../../admin/view/login');       
}
?>
