<?php
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';

date_default_timezone_set('Africa/Lagos');

$user = new User();

$email = "";	
//exit("User: " . $_POST["username"] . " PS: " . $_POST["aky"]);
if(!isset($_POST["email"]) || $_POST["email"] === " " || $_POST["email"] === "-" || trim($_POST["email"]) === ""){
    exit(badRequest("Email is not valid"));
}else{
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
}
if($email === "BAD"){
    exit(badRequest("Email is not valid"));
}

$newpassword = mt_rand(10,111111111);
$user->passwordReset("email,currenttime", "'$email',' . date('Y-m-d H:i:s') . '");
$res = $user->initialChangePassword($email, $newpassword);
if($res === 0){
	//exit(badRequest("Password reset failed"));
	exit("Failed");
}
//end saving the file
$tlog = date("Y-m-d H:i:s");
$tmessage = "You requested a password reset. <br /><br />Below is your new password: <br /><br /> <b>" . $newpassword . "</b>";

    
//send to us
$message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
/*$message .= "Lastname: " . $lastname . "<br />";
$message .= "Firstname: " . $firstname . "<br />";*/
$message .= "Email: " . $email . "<br />";
$message .= "Message:<br /> " . $tmessage . "<br />";
$message .= "</div>";

/*
$mheaders = "MIME-Version: 1.0"  . "\r\n";
$mheaders .= "Content-type:text/html; charset=UTF-8" . "\r\n";		
$subject = "Come and See";
$message = wordwrap($message, 70);	

$mheaders .= "From: info@accsiss.com.ng" . "\r\n"; //$_REQUEST['email'];
$mheaders .= "Reply-To: info@accsiss.com.ng" . "\r\n";
$response = mail($email . ",icisystemng@gmail.com",$subject,$message, $mheaders);    */   

$response = sendEmail($email,$message);

if($response === "Successful"){
    echo json_encode(array("status" => true,"code" => 200,"message" =>$response));
}else{
    echo json_encode(array("status" => false,"code" => 204,"message" =>$response));
}

function sendEmail($email,$message){
    //$link = "https://cowork.com.ng/controllers/verifyuser.php?email=$email";
    //$tmessage = "</b> Click the link below to verify your account: <br /><br /> <span style='padding:7px;background-color:#1E90FF;color:white;'><a href='" . $link . "' style='text-decoration:none;color:white;'>Click this link</a></span>";
    
    //$message ="<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
    //$message .= "<br /> " . $tmessage . "<br />";
    //$message .= "</div>";
    
    $payload = array(
        "email" => $email,
        "message" => $message,
        "subject" => "Elfrique - Forgot Password"
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
function sendEmailOld($mainadmin,$email,$messg){

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
	$mail->Subject = $_REQUEST["lastname"] . " " .  $_REQUEST["firstname"];
	$message = "" . $messg . "";
	//$message = stripslashes($message);
	//$mail->AddEmbeddedImage('Images/blhd2.jpg', 'blhd_2u');
	$mail->Body = "" . $message;

}


?>