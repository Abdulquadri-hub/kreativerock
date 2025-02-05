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

$rowsize = (isset($_REQUEST["rowsize"]) && $_REQUEST["rowsize"] > 0) ? intval($_REQUEST["rowsize"]) : 0;
if($rowsize == 0){ 
    exit(badRequest(204,'Invaid rowsize')); 
}
$tourids = "'";
for($i=1;$i<=$rowsize;$i++){
    $tourid = (isset($_REQUEST["tourid" . $i]) && $_REQUEST["tourid" . $i] > 0) ? intval($_REQUEST["tourid" . $i]) : 0;
    $amount = (isset($_REQUEST["amount" . $i]) && $_REQUEST["amount" . $i] > 0) ? ($_REQUEST["amount" . $i]) : 0;
    if($tourid == 0 || $amount == 0){ 
        exit(badRequest(204,'Invaid tourid or amount')); 
    }
    $tourids .= $tourids . "|";
}

$currency = (isset($_POST["currency"]) && $_POST["currency"] !== "") ? htmlentities($_POST["currency"],ENT_QUOTES) : "-";
$currency = ($currency === "-") ? "NGN" : $currency;

//exit(" Package: " . $package);
$admin = new Administration();
$user= new User();
if($fullname === "-"){
    $resuser = $user->getUserInfo("email = '" . $email . "'");
    if($resuser){
        $fullname = $resuser["lastname"] . " " . $resuser["firstname"];
    }
}
$totalamount = (isset($_REQUEST["totalamount"]) && $_REQUEST["totalamount"] > 0) ? ($_REQUEST["totalamount"]) : 0;

if($totalamount == 0){
    exit(badRequest(204,'Not successful. Totalamount should not be zero or less'));
}

$phone = "";
$transactiondate = date('Y-m-d H:i:s');
$fluttercon = $admin->getFlutterInfo("gateway='FLUTTERWAVE'");
$skey = $fluttercon["secretkey"];

do{
    $ref = mt_rand(1000000, intval(strtotime(date('Y-m-d H:i:s'))));
    $resvoting = $admin->executeByQuerySelector("SELECT * FROM tourpay WHERE transactionref = '$ref'");
}while($resvoting !== nul && count($resvoting) > 0);

$status = "PENDING";
$bookingid = "";
for($i=1;$i<=$rowsize;$i++){
    $fields = "tourid,email,fullname,currency,gateway,description,amount,transactiondate,transactionref,package,qty,status,created_at";
    $values = $_POST["tourid" . $i] . ",'$email','$fullname','$currency','PAYSTACK','$description'," . $_REQUEST["amount" . $i] . ",'$transactiondate',
    '$ref','" . $_POST["package" . $i] . "'," . $_POST["qty" . $i] . ",'$status','$transactiondate'";
    
    $resfunding = $admin->registerTourPay($fields,$values);
    $bookingid .= $resfunding . "|";
}

$customername = $fullname;

$fresult = makePayment($ref,$amount,$email,$phone,$customername,$currency,$skey,$tourids,$bookingid);
$presult = json_decode($fresult,true); 
$accesscode = $presult["data"]["access_code"];
$status = "PENDING";

$bookingid = "";
for($i=1;$i<=$rowsize;$i++){
    $fields = "tourid,email,fullname,currency,gateway,description,amount,transactiondate,transactionref,package,qty,status,created_at";
    $values = $_POST["tourid" . $i] . ",'$email','$fullname','$currency','FLUTTERWAVE','$description'," . $_REQUEST["amount" . $i] . ",'$transactiondate',
    '$ref','" . $_POST["package" . $i] . "'," . $_POST["qty" . $i] . ",'$status','$transactiondate'";
    
    $resfunding = $admin->registerTourPay($fields,$values);
    $bookingid .= $resfunding . "|";
}

if($_POST["totalamount"] > 0 && $resfunding != null){

    $framework = new FrameWork();

    for($i=1;$i<=$rowsize;$i++){
        $rescontest = $framework->getTourPackageInfo("id = " . intval($_POST["tourid" . $i]));
        $resorg = $framework->getOrganisationInfo("id = " . $rescontest["organisationid"]);
    
        $fields = "productid,description,channel,debit,credit,organisationid,user,paymentgateway,paymentmethod,transactiondate,currency,reference,status,tlog";
        $values = $_POST["tourid" . $i] . ",'$description', 'TOURS', 0, " . $_POST["amount" . $i] . "," . $rescontest["organisationid"] . ",
        '" . $resorg["user"] . "','FLUTTERWAVE','CARD','$transactiondate','$currency','$ref','$status','$transactiondate'";
        $restrans = $admin->registerTransaction($fields,$values);
        
        $fields = "productid,description,channel,debit,credit,organisationid,user,paymentgateway,paymentmethod,transactiondate,currency,reference,status,tlog";
        $values = $_POST["tourid" . $i] . ",'$description', 'TOURS', " . $_POST["amount" . $i] . ",0,NULL,
        '$email','FLUTTERWAVE','CARD','$transactiondate','$currency','$ref','$status','$transactiondate'";
        $restrans = $admin->registerTransaction($fields,$values);
    }
}

//echo $presult["data"]["authorization_url"];
echo $presult["data"]["link"];

function makePayment($ref,$amount,$email,$phone,$customername,$currency,$skey,$tourids,$bookingid){ 
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
	$postdata["redirect_url"] = "https://nglohitech.com/elfrique/admin/controllers/hookverifyflutterpaytour?reference=$ref&tourid=$tourids&bookingid=$bookingid";
	
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