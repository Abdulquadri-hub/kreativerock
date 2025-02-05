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
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
if($email === "BAD"){ exit(badRequest(204,'Invaid email')); }

$package = (isset($_POST["package"]) && $_POST["package"] !== "") ? htmlentities($_POST["package"],ENT_QUOTES) : "-";
if($package === "-"){ exit(badRequest(204,'Invaid package')); }

$nameofvoter = (isset($_POST["nameofvoter"]) && $_POST["nameofvoter"] !== "") ? htmlentities($_POST["nameofvoter"],ENT_QUOTES) : "-";
//if($nameofvoter === "-"){ //exit(badRequest(204,'Invaid voter name')); }

$contestid = (isset($_POST["contestid"]) && $_POST["contestid"] > 0) ? intval($_POST["contestid"]) : 0;
if($contestid == 0){ exit(badRequest(204,'Invaid contestid')); }

$candidateid = (isset($_POST["candidateid"]) && $_POST["candidateid"] > 0) ? intval($_POST["candidateid"]) : 0;
if($candidateid == 0){ exit(badRequest(204,'Invaid candidateid')); }

$numberofvotes = (isset($_POST["numberofvotes"]) && $_POST["numberofvotes"] > 0) ? intval($_POST["numberofvotes"]) : 0;
if($numberofvotes == 0){ exit(badRequest(204,'Invaid number of votes')); }

$currency = (isset($_POST["currency"]) && $_POST["currency"] !== "") ? htmlentities($_POST["currency"],ENT_QUOTES) : "-";
$currency = ($currency === "-") ? "NGN" : $currency;

//exit(" Package: " . $package);
$admin = new Administration();
$user= new User();
//$resuser = $user->getUserInfo("id = '" . $_SESSION["user_id"] . "'");

//$amount = (isset($_POST["amount"]) && $_POST["amount"] > 0) ? ($_POST["amount"] * 100) : 0;
$amount = (isset($_POST["amount"]) && $_POST["amount"] > 0) ? ($_POST["amount"]) : 0;
if($amount == 0){
    if($package === "FREE"){
        
    }else{
        exit(badRequest(204,'Not successful. Credit amount should not be zero or less'));
    }
}
$phone = "";
$transactiondate = date('Y-m-d H:i:s');
$fluttercon = $admin->getFlutterInfo("gateway='FLUTTERWAVE'");
$skey = $fluttercon["secretkey"];

do{
    $ref = mt_rand(1000000, intval(strtotime(date('Y-m-d H:i:s'))));
    $resvoting = $admin->executeByQuerySelector("SELECT * FROM voting WHERE transactionref = '$ref'");
}while($resvoting !== nul && count($resvoting) > 0);

$status = "";
if($package !== "FREE"){
    $customername = $numberofvotes;
    
    $fresult = makePayment($ref,$amount,$email,$phone,$customername,$currency,$skey);
    $presult = json_decode($fresult,true); 
    $accesscode = $presult["data"]["access_code"];
    $status = "PENDING";
}else{
    $status = "POSTED";
}
//exit("Resp: " . $fresult . " Package: " . $package);
$fields = "contestid,email,nameofvoter,currency,gateway,package,amount,transactiondate,transactionref,numberofvotes,candidateid,status,created_at";
$values = $contestid . ",'$email','$nameofvoter','$currency','FLUTTERWAVE','$package'," . $_POST["amount"] . ",'$transactiondate',
'$ref',$numberofvotes,$candidateid,'$status','$transactiondate'";

$resfunding = $admin->registerVoting($fields,$values);

if($_POST["amount"] > 0 && $resfunding != null){
    $framework = new FrameWork();
    $rescontest = $framework->getContestInfo("id = $contestid");
    $resorg = $framework->getOrganisationInfo("id = " . $rescontest["organisationid"]);

    $fields = "productid,description,channel,debit,credit,organisationid,user,paymentgateway,paymentmethod,transactiondate,currency,reference,status,tlog";
    $values = $contestid . ",'$numberofvotes votes: $nameofvoter', 'VOTING', 0, " . $_POST["amount"] . "," . $rescontest["organisationid"] . ",
    '" . $resorg["user"] . "','FLUTTERWAVE','CARD','$transactiondate','$currency','$ref','$status','$transactiondate'";
    $restrans = $admin->registerTransaction($fields,$values);

    $fields = "productid,description,channel,debit,credit,organisationid,user,paymentgateway,paymentmethod,transactiondate,currency,reference,status,tlog";
    $values = $contestid . ",'$numberofvotes votes: $nameofvoter', 'VOTING'," . $_POST["amount"] . ",0,NULL,
    '$email','FLUTTERWAVE','CARD','$transactiondate','$currency','$ref','$status','$transactiondate'";
    $restrans = $admin->registerTransaction($fields,$values);
    
}
if($package === "FREE"){
    if($resfunding){
        header("Location: ../../voting/view/payment.php?response=Successful");
        //echo json_encode(array("status"=>true,"code"=>200,"message"=>"Successful"));
    }else{
        header("Location: ../../voting/view/payment.php?response=Failed");
        //echo json_encode(array("status"=>false,"code"=>204,"message"=>"Not successful"));
    }
}else{
    //echo $presult["data"]["authorization_url"];
    echo $presult["data"]["link"];
}

function makePayment($ref,$amount,$email,$phone,$customername,$currency,$skey){ 
	$headers = array("Authorization: Bearer $skey","Content-Type: application/json");
	$postdata = array(
	    "tx_ref" => "", 
		"amount" => "", 
		"email" => "",
		"currency" => "",
		"customer" => "",
		"redirect_url" => ""
		
	    );  //"smsuser=". $user ."&sender=".$sender."&recipients=".$recipients."&messg=".$messg;
	
	$customer = array("email"=>$email,"name"=>$customername, "phonenumber"=>$phone);
	$postdata["tx_ref"] = $ref;
	$postdata["amount"] = $amount;
	$postdata["email"] = $email;
	$postdata["currency"] = $currency;
	$postdata["customer"] = $customer;
	$postdata["redirect_url"] = "https://nglohitech.com/elfrique/admin/controllers/hookverifyflutterpay?reference=$ref";
	
	//"channels" => ["card,bank"]
	$durl = $url; //."?".$getdata;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/payments");
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