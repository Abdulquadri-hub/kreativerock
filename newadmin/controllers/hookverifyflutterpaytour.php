<?php
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
require_once '../../model/Administration.php';

date_default_timezone_set('Africa/Lagos');

$admin = new Administration();
$reference = (isset($_REQUEST["reference"]) && $_REQUEST["reference"] !== "") ? $_REQUEST["reference"] : "-" ;
$txref = (isset($_REQUEST["tx_ref"]) && $_REQUEST["tx_ref"] !== "") ? htmlentities($_REQUEST["tx_ref"],ENT_QUOTES) : "-" ; //$_REQUEST["trxref"];
$status = (isset($_REQUEST["status"]) && $_REQUEST["status"] !== "") ? htmlentities($_REQUEST["status"],ENT_QUOTES) : "-" ; //$_REQUEST["trxref"];
$transaction_id = (isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] !== "") ? htmlentities($_REQUEST["transaction_id"],ENT_QUOTES) : "" ; //$_REQUEST["trxref"];
$tourid = (isset($_REQUEST["tourid"]) && $_REQUEST["tourid"] !== "") ? $_REQUEST["tourid"] : 0 ; //$_REQUEST["trxref"];
$bookingid = (isset($_REQUEST["bookingid"]) && $_REQUEST["bookingid"] !== "") ? $_REQUEST["bookingid"] : 0 ; //$_REQUEST["trxref"];

if($reference === "-"){
    exit(badRequest(204,'Invalid reference'));
}
$fluttercon = $admin->getFlutterInfo("gateway='FLUTTERWAVE'");
$skey = $fluttercon["secretkey"];

if($status === "failed"){
    header("Location: ../../travelandtours/view/paymenttours?response=" . badRequest(204,'Failed') . "&tourid=$tourid&bookingid=$bookingid");
}else{
    $response = verifyPayment($transaction_id,$skey);
    $datarow = json_decode($response,true);
    if($datarow["status"] === "success"){
        if($datarow["data"]["status"] === "successful"){
            $walletress = $admin->executeByQuerySelector("SELECT * FROM tourpay WHERE transactionref = '$txref'");
            if($walletress){
                foreach($walletress as $walletres){
                    $walletupdate = $admin->executeByQuerySelector("UPDATE tourpay SET status = 'POSTED' WHERE id = " . $walletres["id"]);
                    $transupdate = $admin->executeByQuerySelector("UPDATE transactions SET status = 'POSTED' WHERE productid = " . $walletres["tourid"] . " AND channel = 'TOURS'");
                }                
            }
        }
    }
    header("Location: ../../travelandtours/view/paymenttours?response=$response&tourid=$tourid&bookingid=$bookingid");
}


function verifyPayment($transaction_id,$skey){
    $headers = array("Authorization: Bearer $skey");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify");
    //curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
    //curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt ($ch, CURLOPT_POST, 1); 
    //curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;    
}   	
    
?>