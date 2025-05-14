<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

date_default_timezone_set('Africa/Lagos');

if($_SESSION["user_id"] === null || $_SESSION["user_id"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    //exit(json_encode($response));
}

$user = new User();
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
if($email === "BAD"){
    exit(badRequest(204,'Bad email'));
}
$firstname = ((isset($_POST["firstname"]) && $_POST["firstname"] !== "") ? $_POST["firstname"] : "-" );
$lastname = ((isset($_POST["lastname"]) && $_POST["lastname"] !== "") ? $_POST["lastname"] : "-" );
$country = ((isset($_POST["country"]) && $_POST["country"] !== "") ? $_POST["country"] : "-" );
$message = ((isset($_POST["message"]) && $_POST["message"] !== "") ? $_POST["message"] : "-" );

if($firstname === "-" || $lastname === "-" || $country ==="-" || $message === "-"){
    exit(badRequest(204,"Not Successful. You should fill all the form fields"));
}
$tlog = date("Y-m-d H:i:s");
$tmessage = "Contact: <b> " .  $firstname . " " . $lastname . " </b><br />";
$tmessage .= "Coutry: <b> " .  $country . " </b><br /><br  /> $message";

$message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
//$message .= "Email: " . $userres["email"] . "<br />";
$message .= "<br /> " . $tmessage . "<br />";
$message .= "</div>";

//$resp = sendEmail("info@elfrique.com",$message);
$resp = sendEmail("icisystemng@gmail.com",$message);
if($resp === "Successful"){
    echo(json_encode(array("status"=>true,"code"=>200,"message"=>$resp)));  
}else{
    echo(json_encode(array("status"=>false,"code"=>204,"message"=>$resp)));  
}
//$user->addUserActivity($_SESSION["phone"], $_SESSION["phone"] . " | Fialed attempt to reset pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");


function sendEmail($email,$message){
    //$link = "https://cowork.com.ng/controllers/verifyuser.php?email=$email";
    //$tmessage = "</b> Click the link below to verify your account: <br /><br /> <span style='padding:7px;background-color:#1E90FF;color:white;'><a href='" . $link . "' style='text-decoration:none;color:white;'>Click this link</a></span>";
    
    //$message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
    //$message .= "<br /> " . $tmessage . "<br />";
    //$message .= "</div>";
    
    $payload = array(
        "email" => $email,
        "message" => $message,
        "subject" => "Elfrique Contact Enquiry"
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