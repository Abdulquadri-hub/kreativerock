<?php

session_start();

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";


require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/SmsPackage.php';
require_once $rootFolder . 'model/SmsTransaction.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$smsTransaction = new SmsTransaction();

$user = $_SESSION["elfuseremail"] ??  null;
$transactionReference = isset($_REQUEST['reference']) && $_REQUEST['reference'] !== ""  ?  $_REQUEST['reference'] :  "";
$status = isset($_REQUEST['status']) && $_REQUEST['status'] !== ""  ?  $_REQUEST['status'] :  "";
$startDate = isset($_POST['start_date']) && $_POST['start_date'] !== "" ?  $_POST['start_date'] :  "";
$endDate = isset($_POST['end_date']) && $_POST['end_date'] !== "" ?  $_POST['end_date'] :  date("Y-m-d H:i:s");

if(!empty($user))
{
    //
    
    if(!empty($startDate)){
        $resultByUser =  $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE user = '" . $user . "' AND WHERE created_at BETWEEN $startDate AND $endDate AND status = 'COMPLETED'ORDER BY id DESC");
    }else {
        $resultByUser = $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE user = '" . $user . "' AND status = 'COMPLETED' ORDER BY id DESC");
    }
    
    if($resultByUser){
        echo json_encode([
            'status' => true, 
            "code" => 200,
            "message" => "Transaction history retrieved successfully", 
            "data" => $resultByUser
        ]);
        
    }else {
        echo badRequest(204,'No Transaction History Found!');
    }
    
}elseif(!empty($transactionReference)){
    
        
    if(!empty($startDate)){
        $resultByTransactionReference =  $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE reference = '" . $transactionReference . "'  AND created_at BETWEEN $startDate AND $endDate AND status = 'COMPLETED' ORDER BY id DESC");
    }else {
        $resultByTransactionReference = $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE reference = '" . $transactionReference . "' AND status = 'COMPLETED'  ORDER BY id DESC");
    }
    
    
    if($resultByTransactionReference){
       echo json_encode([
            'status' => true, 
            "code" => 200,
            "message" => "Transaction history retrieved successfully", 
            "data" => $resultByTransactionReference
        ]);
    }else {
        
        echo badRequest(204,'No Transaction History Found!');
    }
    
}elseif(!empty($status)){
    
        
    if(!empty($startDate)){
        $resultByStatus =  $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE status = '" . $status . "'  \AND created_at BETWEEN $startDate AND $endDate AND status = 'COMPLETED' ORDER BY id DESC");
    }else {
        $resultByStatus = $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE status = '" . $status . "'  ORDER BY id DESC");
    }
    
    
    if($resultByStatus)
    {
       echo json_encode([
            'status' => true, 
            "code" => 200,
            "message" => "Transaction history retrieved successfully", 
            "data" => $resultByStatus
        ]);
    }else {
        
        echo badRequest(204,'No Transaction History Found!');
    }
    
}else {
    
    if(!empty($startDate)){
        $result =  $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions WHERE created_at BETWEEN $startDate AND $endDate AND status = 'COMPLETED'ORDER BY id DESC");
    }else {
        $result = $smsTransaction->retrieveByQuerySelector("SELECT * FROM sms_transactions ORDER BY id DESC");
    }
    
    
    if($result)
    {
        echo json_encode([
            'status' => true, 
            "code" => 200,
            "message" => "Transaction history retrieved successfully", 
            "data" => $result
        ]);
    }else {
        
        echo badRequest(204,'No Transaction History Found!');
    }
}