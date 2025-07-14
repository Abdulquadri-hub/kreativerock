<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$package = new Package();
$purchase = new Purchase();
$transaction = new Transactions();
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

if($status === "failed"){
    $redirectTo =  $callbackUrl . "&packageid=$packageid&status=$status";
    header("Location: $redirectTo");
    
}else{
    
    $response = $transaction->verifyPayment($transaction_id,$skey);
    
    $datarow = json_decode($response,true);
    if($datarow["status"] === "success")
    {
        if($datarow["data"]["status"] === "successful")
        {
            $purchasedPackages = $admin->executeByQuerySelector("SELECT * FROM purchases WHERE transactionref = '$reference'");
            if($purchasedPackages)
            {
                foreach($purchasedPackages as $purchasedPackage)
                {
                    // $packagePurchasedUpdate = $admin->executeByQuerySelector("UPDATE purchases SET status = 'Active' WHERE id = " . $purchasedPackage["id"]);
                    $transactionUpdate = $admin->executeByQuerySelector("
                                                    UPDATE transactions SET status = 'COMPLETED' 
                                                    WHERE packageid = " . $purchasedPackage["packageid"] . " 
                                                    AND reference = " .$purchasedPackage['transactionref']." ");
                }
               
                $transaction->createOrUpdateUserUnits($reference, $email);
            }
            
            $flutterResponse = json_encode($datarow["data"]);
            $redirectTo =  $callbackUrl . "&packageid=$packageid&response=$flutterResponse";
            header("Location: $redirectTo");
        }
        
    }else {
        
        exit(json_encode(['status' => $datarow['status'],  "message" => $datarow['message'], "tx_ref" => $datarow['data']['tx_ref']]));
    }

}





