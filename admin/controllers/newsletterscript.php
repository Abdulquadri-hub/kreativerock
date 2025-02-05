<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';
date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
if($email === "BAD"){
    exit(badRequest(204,'Bad email'));
}
$framework = new FrameWork();
$res = $framework->getNewsLetterInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        owner = '" . ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? $_POST["owner"] : $res['owner']) . "',
        email = '" . ((isset($_POST["email"]) && $_POST["email"] !== "") ? $_POST["email"] : $res['email']) . "',
        title = '" . ((isset($_POST["title"]) && $_POST["title"] !== "") ? htmlentities($_POST["title"],ENT_QUOTES) : $res['title'] ) . "',
        status = '" . ((isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : $res['status'] ) . "'";

    $result = $framework->updateNewsLetterDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful. May already exist");
    }
    
}else{
    
    // fields names
    $fields = "owner,title,email,status,tlog";
    $values = "
    '" . ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? $_POST["owner"] : "TRAVELS AND TOURS" ) . "',
    '" . ((isset($_POST["title"]) && $_POST["title"] !== "") ? htmlentities($_POST["title"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["email"]) && $_POST["email"] !== "") ? $_POST["email"] : "." ) . "',
    '" . ((isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "DISPLAY" ) . "',
    '" . $_SESSION["elfuseremail"] . " | " . date("Y-m-d H:i:s") . "'";

    $result = $framework->registerNewsLetter($fields, $values);
    if($result){
        $resp = sendEmail($email);
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful. May already exist");
    }
}

function sendEmail($email){
    //$emailencoded = base64_encode($email);
    //$vercode = base64_encode($verificationcode);
    
    //$link = "https://nglohitech.com/elfrique/framework/controllers/verifyuser.php?email=$emailencoded&vercode=$vercode";
    $tmessage = "Good day, <br /><br /> Thank you for your subscription of Elfrique newsletter. <br /><br /> Kindly confirm/verify your email subscription.<br /><br /> Thank you.";
    $tmessage .= "<br /><br /><br /> Kind regards, <br />Elfrique Team";
    
    $message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
    $message .= "<br /> " . $tmessage . "<br />";
    $message .= "</div>";
    
    $payload = array(
        "email" => $email,
        "message" => $message,
        "subject" => "Elfrique Newsletter Subscription"
        );
    	
    $payload = json_encode($payload);
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://comeandsee.com.ng/mailer/sendmailtoElfrique.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "content-type: application/json"
        ),
    ));
    
    $result = curl_exec($curl);
    //$err = curl_error($curl);
    return $result;
    
}
