<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$user = new User();
$WhatsAppPackage = new WhatsAppPackage();
$WhatsAppPurchase = new WhatsAppPurchase();
$WhatsAppTransaction = new WhatsAppTransaction();
$admin = new Administration();

$email = (isset($_REQUEST['elfuseremail']) && $_REQUEST['elfuseremail'] !== "" ? $_REQUEST['elfuseremail'] : "");

if($email == ""){
    exit(json_encode(["status" => false, "code" => 400, "message" => "email is required"]));
}

$res = $user->getUserInfo("email = '" . $email . "'");
if($res)
{
    $user = $email;
    $packageid = (isset($_REQUEST['packageid']) && $_REQUEST['packageid'] !== "" ? htmlentities($_REQUEST["packageid"],ENT_QUOTES) : "");
    $qty = (isset($_REQUEST['qty']) && $_REQUEST['qty'] !== "" ? $_REQUEST['qty'] : "");
    $description = (isset($_REQUEST["description"]) && $_REQUEST["description"] !== "") ? htmlentities($_REQUEST["description"],ENT_QUOTES) : "-";
    $currency = (isset($_POST["currency"]) && $_POST["currency"] !== "") ? htmlentities($_POST["currency"],ENT_QUOTES) : "-";
    $callbackUrl = (isset($_POST["callback"]) && $_POST["callback"] !== "") ? htmlentities($_POST["callback"],ENT_QUOTES) : "-";

    if((empty($packageid))  ||  (empty($qty) || $qty <= 0) )
    {
        exit(json_encode(["status" => false, "code" => 204, "messsage" => "Bad Request"]));
    }
    
    $package = $WhatsAppPackage->getWhatsAppPackageInfo("id = '" . $packageid . "'");
    if(empty($package))
    {
        exit(json_encode(["status" => false, "code" => 400, "messsage" => "Package Not Found!"]));
    }
    
    $amount = (isset($_REQUEST["amount"]) && $_REQUEST["amount"] > 0) ? ($_REQUEST["amount"]) : 0;
    if($amount == 0){
        exit(badRequest(204,'Not successful. Amount should not be zero or less'));
    }
    
    
    $currency = ($currency === "-") ? "NGN" : $currency;
    $transactiondate = date('Y-m-d H:i:s');
    $ref = mt_rand(1000000, intval(strtotime(date('Y-m-d H:i:s'))));
    $fluttercon = $admin->getFlutterInfo("gateway='FLUTTERWAVE'");
    $skey = $fluttercon["secretkey"];
    $customername = $res["lastname"] . " " . $res["firstname"];
    $phone = "";
    
    $payment = $WhatsAppTransaction->makePayment($ref,$amount,$email,$phone,$customername,$currency,$skey,$packageid,$callbackUrl);
    $paymentResponse = json_decode($payment, true);
    if($paymentResponse['status'] === "success")    
    {
        
        $fields = "`user`, `packageid`, `qty`, `amount`, `transactionref`";
        $values = " '" . $user . "', '" . $packageid . "', '" . $qty . "', '" . $amount . "', '" . $ref . "' ";
    
        $result = $WhatsAppPurchase->registerWhatsAppPurchase($fields, $values);
        if($result)
        {
            $message = 'Purchase of WHATSAPP ' . $qty . ' units from package ' . $package['packagename'];
            $status = "PENDING";
            $gateway = "FLUTTERWAVE";
            $paymentmethod = "CARD";
            $transactionFields = " `user`,`packageid`,`qtyin`,`qtyout`,`message`,`amount`, `gateway`, `description`, `reference`, `paymentmethod`,  `status` ";
            $transactionValues = " '" . $user . "', '" . $packageid . "', '" . $qty . "', '" . 0 . "', '" . $message . "', '" . $amount . "', '" . $gateway . "', '" . $description . "',  '" . $ref . "', '" . $paymentmethod . "', '" . $status . "' ";
        
            $transaction = $WhatsAppTransaction->registerWhatsAppTransaction($transactionFields, $transactionValues);
            if($transaction)
            {
                //
                
            }else {
            
                exit(json_encode(["status" => false, "code" => 500, "message" => "Whatsapp Transaction Not Successful."]));
            }
            
            echo json_encode(['status' => true, "code" => 200, "data" => $paymentResponse]);
        
        
        }else {
        
            exit(json_encode(["status" => false, "code" => 500, "message" => "Whatsapp Checkout Not Successful."]));
        }
          
    }else {
        
        exit(json_encode(["status" => false, "code" => 500, "message" => "Payment Not Successful."]));
    }
    
}else {
    
    exit(json_encode(["status" => false, "code" => 400, "messsage" => "User Not Found"]));
}


