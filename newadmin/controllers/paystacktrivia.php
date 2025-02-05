<?php

//session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
require_once '../../model/Administration.php';
require_once '../../model/FrameWork.php';

date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail']) || $_SESSION["user_id"] === null || !isset($_SESSION["user_id"])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

//$email = $_SESSION['elfuseremail'];
$email = filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL) ? $_REQUEST["email"] : "BAD";
if($email === "BAD"){ exit(badRequest(204,'Invaid email')); }

$description = (isset($_REQUEST["description"]) && $_REQUEST["description"] !== "") ? htmlentities($_REQUEST["description"],ENT_QUOTES) : "-";
//if($package === "-"){ exit(badRequest(204,'Invaid package')); }

$fullname = (isset($_REQUEST["fullname"]) && $_REQUEST["fullname"] !== "") ? htmlentities($_REQUEST["fullname"],ENT_QUOTES) : "-";

$triviaid = (isset($_REQUEST["triviaid"]) && $_REQUEST["triviaid"] > 0) ? intval($_REQUEST["triviaid"]) : 0;
if($triviaid == 0){ exit(badRequest(204,'Invaid triviaid')); }

$currency = "NGN";

//exit(" Package: " . $package);
$admin = new Administration();
$user= new User();
//$resuser = $user->getUserInfo("id = '" . $_SESSION["user_id"] . "'");
if($fullname === "-"){
    $resuser = $user->getUserInfo("email = '" . $email . "'");
    if($resuser){
        $fullname = $resuser["lastname"] . " " . $resuser["firstname"];
    }
}
$amount = (isset($_REQUEST["amount"]) && $_REQUEST["amount"] > 0) ? ($_REQUEST["amount"] * 100) : 0;
if($amount == 0){
    exit(badRequest(204,'Not successful. Credit amount should not be zero or less'));
}
$transactiondate = date('Y-m-d H:i:s');

do{
    $ref = mt_rand(100000000, 99999999999);
    $resvoting = $admin->executeByQuerySelector("SELECT * FROM triviapay WHERE transactionref = '$ref'");
}while($resvoting !== null && count($resvoting) > 0);

$status = "PENDING";

//if($package !== "FREE"){
    $fresult = makePayment($ref,$amount,$email,$triviaid);
    $presult = json_decode($fresult,true); 
    $accesscode = $presult["data"]["access_code"];
    //$status = "PENDING";
//}else{
    //$status = "POSTED";
//}*/
//exit("Resp: " . $fresult . " Package: " . $package);
$fields = "triviaid,email,fullname,currency,gateway,description,amount,transactiondate,transactionref,status,created_at";
$values = $triviaid . ",'$email','$fullname','$currency','PAYSTACK','$description'," . $_REQUEST["amount"] . ",'$transactiondate',
'$ref','$status','$transactiondate'";

$resfunding = $admin->registerTriviaPay($fields,$values);

if($_POST["amount"] > 0 && $resfunding != null){
    $framework = new FrameWork();
    $rescontest = $framework->getTriviaTestInfo("id = $triviaid");
    $resorg = $framework->getOrganisationInfo("id = " . $rescontest["organisationid"]);

    $fields = "productid,description,channel,debit,credit,organisationid,user,paymentgateway,paymentmethod,transactiondate,currency,reference,status,tlog";
    $values = $triviaid . ",'$description', 'TRIVIA', 0, " . $_POST["amount"] . "," . $rescontest["organisationid"] . ",
    '" . $resorg["user"] . "','PAYSTACK','CARD','$transactiondate','$currency','$ref','$status','$transactiondate'";
    $restrans = $admin->registerTransaction($fields,$values);
    
    $fields = "productid,description,channel,debit,credit,organisationid,user,paymentgateway,paymentmethod,transactiondate,currency,reference,status,tlog";
    $values = $triviaid . ",'$description', 'TRIVIA', " . $_POST["amount"] . ",0,NULL,
    '$email','PAYSTACK','CARD','$transactiondate','$currency','$ref','$status','$transactiondate'";
    $restrans = $admin->registerTransaction($fields,$values);
}
/*
if($package === "FREE"){
    if($resfunding){
        header("Location: ../../voting/view/payment.php?response=Successful");
        //echo json_encode(array("status"=>true,"code"=>200,"message"=>"Successful"));
    }else{
        header("Location: ../../voting/view/payment.php?response=Failed");
        //echo json_encode(array("status"=>false,"code"=>204,"message"=>"Not successful"));
    }
}else{*/
    echo $presult["data"]["authorization_url"];
//}

function makePayment($ref,$amount,$email,$triviaid){ 
	$headers = array("Authorization: Bearer sk_test_331addae81e5c25bbe28d61f540561b77114dad5","Content-Type: application/json");
	$postdata = array(
	    "reference" => "", 
		"amount" => "", 
		"email" => "",
		"callback_url" => ""
		
	    );  //"smsuser=". $user ."&sender=".$sender."&recipients=".$recipients."&messg=".$messg;
	
	$postdata["reference"] = $ref;
	$postdata["amount"] = $amount;
	$postdata["email"] = $email;
	$postdata["callback_url"] = "https://nglohitech.com/elfrique/admin/controllers/hookverifypaystacktrivia.php?reference=$ref&triviaid=$triviaid";
	
	//"channels" => ["card,bank"]
	$durl = $url; //."?".$getdata;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/initialize/");
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
	//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_POST, 1); 
	curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;    
} 

?>