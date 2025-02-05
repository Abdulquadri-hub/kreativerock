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
$txref = (isset($_REQUEST["trxref"]) && $_REQUEST["trxref"] !== "") ? htmlentities($_REQUEST["trxref"],ENT_QUOTES) : "-" ; //$_REQUEST["trxref"];
if($reference === "-"){
    //exit(badRequest(204,'Invalid reference'));
    header("Location: ../../tickets/view/payment.php?response=" . badRequest(204,'Invalid reference'));
}else{
    $response = verifyPayment($reference);
    $datarow = json_decode($response,true);
    if($datarow["status"]){
        if($datarow["data"]["status"] === "success"){
            $walletres = $admin->getVotingInfo("transactionref = '$reference'");
            if($walletres){
                $walletupdate = $admin->executeByQuerySelector("UPDATE voting SET status = 'POSTED' WHERE id = " . $walletres["id"]);
                $transupdate = $admin->executeByQuerySelector("UPDATE transactions SET status = 'POSTED' WHERE productid = " . $walletres["contestid"] . " AND channel = 'VOTING'");
            }
        }
    }
    
    header("Location: ../../voting/view/payment.php?response=$response");
}
function verifyPayment($ref){
    $headers = array("Authorization: Bearer sk_test_331addae81e5c25bbe28d61f540561b77114dad5");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/".$ref);
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