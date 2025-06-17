<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$user = new User();
$smslog = new SMSLog();

$ver_code = (isset($_GET["vercode"]) && $_GET["vercode"] !== "") ? htmlentities($_GET["vercode"],ENT_QUOTES) : "";
$ver_email = (isset($_GET["email"]) && $_GET["email"] !== "") ? htmlentities($_GET["email"],ENT_QUOTES) : "";

if($ver_code === ""){
    $resp = array("status" => false,"code" => 204,"message" => "Email verification code is invalid");
    exit(badRequest($resp));
}

$ver_code = base64_decode($ver_code); 
$ver_email = base64_decode($ver_email); 

$resemail = $user->getVerificationLog($ver_email);

if($resemail["verificationcode"] !== $ver_code){
    $resp = array("status" => false,"code" => 204,"message" => "We could not verify the email");
    exit(json_encode($resp));
}

$emailresetres = $user->updateVerificationLog($resemail["id"]);
if($emailresetres){
    $user->addUserActivity($ver_email, "Email verification at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "USER VERIFIED");

    $userId = $user->getUserIdByEmail($ver_email);
    $user->updateUserDetails("status = 'VERIFIED'", $userId);

    $userDetails =  $user->getUserByEmail($ver_email);
    $userName = $userDetails['firstname'] . ' ' . $userDetails['lastname'];

    $dashboardLink = "";
    $profileLink = "";
    
    $user->sendWelcomeEmail($ver_email, $userName, $dashboardLink, $profileLink);

    redirect();
    // $resp = array("status" => true,"code" => 200,"message" => "Successful");
    // exit(success($resp));
    
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
    
    header('Location: ../../newadmin/view/login');       
}

function redirect(){
    header('Location: ../../newadmin/view/login');       
}
