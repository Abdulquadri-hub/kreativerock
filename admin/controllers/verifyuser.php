<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if(isset($_SESSION["elfuseremail"]) && $_SESSION["elfuseremail"] === null || $_SESSION["elfuseremail"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    //exit(json_encode($response));
    logoff();
}

$user = new User();

$smslog = new SMSLog();
$email = $_SESSION["elfuseremail"] ?? null; //$user->getEscapedString(escape($_POST["phone"]));
$ver_code = (isset($_GET["vercode"]) && $_GET["vercode"] !== "") ? htmlentities($_OET["vercode"],ENT_QUOTES) : "";
if($ver_code === ""){
    logoff();
    $resp = array("status" => false,"code" => 204,"message" => "Email verification code is invalid");
    //exit(json_encode($resp));
}

$ver_code = base64_decode($ver_code); 

$resemail = $smslog->retrieveByQuerySelector("select * from verificationlogs where email = '" . $email . "' AND status LIKE 'EMAILVERIFY' ORDER BY id DESC");

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
    
    $resp = array("status" => true,"code" => 200,"message" => "Successful");
    exit(json_encode($resp));
    
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
