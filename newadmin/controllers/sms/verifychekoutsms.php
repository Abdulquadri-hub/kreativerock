<?php

session_start();

$rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";


require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/SmsPackage.php';
require_once $rootFolder . 'model/SmsPurchase.php';
require_once $rootFolder . 'model/SmsTransaction.php';
require_once $rootFolder . 'model/Administration.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$smsPackage = new SmsPackage();
$smsPurchase = new SmsPurchase();
$smsTransaction = new SmsTransaction();
$admin = new Administration();


$reference = (isset($_REQUEST["reference"]) && $_REQUEST["reference"] !== "") ? htmlentities($_REQUEST["reference"],ENT_QUOTES) : "-" ; 
$txref = (isset($_REQUEST["transactionref"]) && $_REQUEST["transactionref"] !== "") ? htmlentities($_REQUEST["transactionref"],ENT_QUOTES) : "-" ; 
$status = (isset($_REQUEST["status"]) && $_REQUEST["status"] !== "") ? htmlentities($_REQUEST["status"],ENT_QUOTES) : "-" ; 
$transaction_id = (isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] !== "") ? htmlentities($_REQUEST["transaction_id"],ENT_QUOTES) : "" ;
$packageid = (isset($_REQUEST["packageid"]) && $_REQUEST["packageid"] !== "") ? $_REQUEST["packageid"] : 0 ;
$callbackUrl = (isset($_REQUEST["callback"]) && $_REQUEST["callback"] !== "") ? htmlentities($_REQUEST["callback"],ENT_QUOTES) : "-";
$email = $_SESSION["elfuseremail"] ??  null;

if($reference === "-"){
    exit(badRequest(204,'Invalid Transaction Reference'));
}
$fluttercon = $admin->getFlutterInfo("gateway='FLUTTERWAVE'");
$skey = $fluttercon["secretkey"];

if($status === "failed")
{
   
    $redirectTo =  $callbackUrl . "&packageid=$packageid&status=$status";
    header("Location: $redirectTo");
    
}else{
    
    $response = $smsTransaction->verifyPayment($transaction_id,$skey);
    
    $datarow = json_decode($response,true);
    if($datarow["status"] === "success")
    {
        if($datarow["data"]["status"] === "successful")
        {
            $purchasedPackages = $admin->executeByQuerySelector("SELECT * FROM sms_purchases WHERE transactionref = '$reference'");
            if($purchasedPackages)
            {
                foreach($purchasedPackages as $purchasedPackage)
                {
                    // $smsPackagePurchasedUpdate = $admin->executeByQuerySelector("UPDATE sms_purchases SET status = 'Active' WHERE id = " . $purchasedPackage["id"]);
                    $smsTransactionUpdate = $admin->executeByQuerySelector("
                                                    UPDATE sms_transactions SET status = 'COMPLETED' 
                                                    WHERE packageid = " . $purchasedPackage["packageid"] . " 
                                                    AND reference = " .$purchasedPackage['transactionref']." ");
                }
               
                $smsTransaction->createOrUpdateUserUnits($reference, $email);
            }
            
            $flutterResponse = json_encode($datarow["data"]);
            $redirectTo =  $callbackUrl . "&packageid=$packageid&response=$flutterResponse";
            header("Location: $redirectTo");
        }
        
    }else {
        
        exit(json_encode(['status' => $datarow['status'],  "message" => $datarow['message'], "tx_ref" => $datarow['data']['tx_ref']]));
    }

}





