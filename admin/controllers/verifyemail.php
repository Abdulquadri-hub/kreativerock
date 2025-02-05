<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
date_default_timezone_set('Africa/Lagos');

if($_SESSION["user_id"] === null || $_SESSION["user_id"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    exit(json_encode($response));
}

$resp = array("status" => false,"code" => "204","message"=>"");
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : $_SESSION["elfuseremail"];
if( $email === "-"){
    $resp["message"] = "email is invalid";
    //$user->addUserActivity($phone, "Failed Attempt to change pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    //exit(json_encode($resp));
}

$user = new User();
$resuser = $user->getUserInfo("id = '" . $_SESSION["user_id"] . "'");
if($resuser){
    //$resultupdate = $user->updateUserDetails("email='" . $email . "'", $_SESSION["user_id"]);
    //if($resultupdate){
    //    $_SESSION["email"] = $email;
    //}
}else{
    $resp["message"] = "Something is wrong with the session data";
    exit(json_encode($resp));
}

    $messg = $verificationcode = mt_rand(1000000000,9999999999);
    errorhandler("Email verification: " . $verificationcode);
    
    $tlog = date("D, d M Y  H:m:s");
    $status = "EMAILVERIFY";
    $customer_sms = "YES";            
    //sendSMS($phone, $messg, $phone, $customer_sms,"RESET");
    $fields = "date,email,status,verificationcode,tlog";
    $values = "'" . date('Y-m-d H:i:s') . "','" . $email . "','" . $status . "','" . $messg . "', '" . $tlog . "'";
    $dbmodel = new Model();
    $dbmodel->insertdata('verificationlogs', $fields, $values);
    
    $resp["message"] = "Successful";
    $resp["status"] = true;
    $resp["code"] = "200";
    //$user->addUserActivity($mail, $_SESSION["phone"] . " | reset pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    
    $tlog = date("Y-m-d H:i:s");
    $tmessage = "Your email: <b>" . $email . "</b> verification code is: <br /><br />" . $messg;

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
    sendEmail($email,$message,$verificationcode);
    
    //$user->addUserActivity($_SESSION["phone"], $_SESSION["phone"] . " | Fialed attempt to reset pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    echo(json_encode($resp));  

function sendEmail($email,$message,$verificationcode){
    $emailencoded = base64_encode($email);
    $vercode = base64_encode($verificationcode);
    
    $link = "https://nglohitech.com/elfrique/framework/controllers/verifyuser.php?email=$emailencoded&vercode=$vercode";
    $tmessage = "</b> Click the link below to verify your account: <br /><br /> <span style='padding:7px;background-color:#1E90FF;color:white;'><a href='" . $link . "' style='text-decoration:none;color:white;'>Click this link</a></span>";
    
    $message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
    $message .= "<br /> " . $tmessage . "<br />";
    $message .= "</div>";
    
    $payload = array(
        "email" => $email,
        "message" => $message,
        "subject" => "Elfrique Email Verifiction"
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