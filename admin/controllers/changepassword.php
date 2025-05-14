<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

date_default_timezone_set('Africa/Lagos');

if($_SESSION["user_id"] === null || $_SESSION["user_id"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    exit(json_encode($response));
}

$user = new User();
$oldpassword = (isset($_POST["oldupw"]) && $_POST["oldupw"] !== "") ? escape($_POST["oldupw"]) : "-";
$newpassword = (isset($_POST["newupw"]) && $_POST["newupw"] !== "") ? escape($_POST["newupw"]) : "-";
if($oldpassword === "-" || $newpassword === "-"){
    $response = array("status" => false,"code" => 204,"message" => "Invalid passwords");
    exit(json_encode($response));    
}

$email = $_SESSION["elfuseremail"];
if(!isset($email) || $email === null){
    $resp["message"] = "Credentials invalid";
    //$user->addUserActivity($phone, "Failed Attempt to change pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    exit(json_encode($resp));
}

$resuser = $user->changePassword($email, $oldpassword, $newpassword);

if($resuser){
    $resp["message"] = "Successful";
    $resp["status"] = true;
    $resp["code"] = "200";
}else{
    $resp["message"] = "Not successful. Invalid credentials";
    $resp["status"] = false;
    $resp["code"] = "204";   
    exit(json_encode($resp));
}
    $messg = $verificationcode = mt_rand(1000000000,9999999999);
    errorhandler("Email verifivation: " . $verificationcode);
    
    /*
    $tlog = date("D, d M Y  H:m:s");
    $status = "EMAILVERIFY";
    $customer_sms = "YES";            
    //sendSMS($phone, $messg, $phone, $customer_sms,"RESET");
    $fields = "date,accountnumber,status,verificationcode,tlog";
    $values = "'" . date('Y-m-d H:i:s') . "','" . $email . "','" . $status . "','" . $messg . "', '" . $tlog . "'";
    $dbmodel = new Model();
    $dbmodel->insertdata('smslogs', $fields, $values);*/
    

    //$user->addUserActivity($mail, $_SESSION["phone"] . " | reset pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    
    $tlog = date("Y-m-d H:i:s");
    $tmessage = "Your password was just changed by : <b>" . $email . "</b>";;

    $message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
    //$message .= "Email: " . $userres["email"] . "<br />";
    $message .= "<br /> " . $tmessage . "<br />";
    $message .= "</div>";
    /*
    $mheaders = "MIME-Version: 1.0"  . "\r\n";
    $mheaders .= "Content-type:text/html; charset=UTF-8" . "\r\n";		
    $subject = "How To Grow: Verification";
    $message = wordwrap($message, 70);	

    $mheaders .= "From: info@nglohitech.com" . "\r\n"; //$_REQUEST['email'];
    $mheaders .= "Reply-To: info@nglohitech.com" . "\r\n";
    $response = mail($email,$subject,$message, $mheaders); 
    */
    sendEmail($email,$message);
    
    //$user->addUserActivity($_SESSION["phone"], $_SESSION["phone"] . " | Fialed attempt to reset pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    echo(json_encode($resp));  

function sendEmail($email,$message){
    //$link = "https://cowork.com.ng/controllers/verifyuser.php?email=$email";
    //$tmessage = "</b> Click the link below to verify your account: <br /><br /> <span style='padding:7px;background-color:#1E90FF;color:white;'><a href='" . $link . "' style='text-decoration:none;color:white;'>Click this link</a></span>";
    
    //$message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
    //$message .= "<br /> " . $tmessage . "<br />";
    //$message .= "</div>";
    
    $payload = array(
        "email" => $email,
        "message" => $message,
        "subject" => "Elfrique Password Change"
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
/*
function sendEmail($mainadmin,$email,$messg){

	//$postdata = "mainadmin=". $mainadmin ."&email=".$email."&messg=".$messg;
	require("class.phpmailer.php");
	$mail = new PHPMailer();
	//$mail->IsMail();
	$mail->IsSMTP();
	$mail->Host = "localhost";
	$mail->IsHTML(true);
	$mail->AddAddress($mainadmin);
    $mail->AddCC($email);
	//$mail->AddCC("miratechnologiesng@gmail.com");
    $mail->FromName = "AFFIN";
	$mail->Subject = $_SESSION["lastname"] . " " .  $_SESSION["firstname"];
	$message = "" . $messg . "";
	//$message = stripslashes($message);
	//$mail->AddEmbeddedImage('Images/blhd2.jpg', 'blhd_2u');
	$mail->Body = "" . $message;

}*/


?>