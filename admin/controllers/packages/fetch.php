<?php

session_start();

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";


require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/SmsPackage.php';
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}


$smsPackage = new SmsPackage();

$packageId = isset($_REQUEST['id']) && $_REQUEST['id'] !== ""  ?  $_REQUEST['id'] :  "";
$startDate = isset($_POST['start_date']) && $_POST['start_date'] !== "" ?  $_POST['start_date'] :  "";
$endDate = isset($_POST['end_date']) && $_POST['end_date'] !== "" ?  $_POST['end_date'] :  date("Y-m-d H:i:s");

if(!empty($packageId))
{
    //
    
    if(!empty($startDate))
    {
        $result =  $smsPackage->retrieveByQuerySelector("SELECT * FROM sms_packages WHERE id = $packageId AND WHERE created_at BETWEEN $startDate AND $endDate ORDER BY id DESC");
    }else {
        
        $result = $smsPackage->retrieveByQuerySelector("SELECT * FROM sms_packages WHERE id = $packageId ORDER BY id DESC");
    }
    
    
    if($result)
    {
        echo  success($result,200, "Successful","Successful");
    }else {
        
        echo badRequest(204,'No Package Found!');
    }
    
}else {
    
    if(!empty($startDate))
    {
        
        $result =  $smsPackage->retrieveByQuerySelector("SELECT * FROM sms_packages WHERE created_at BETWEEN $startDate AND $endDate ORDER BY id DESC");
    }else {
        
        $result = $smsPackage->retrieveByQuerySelector("SELECT * FROM sms_packages ORDER BY id DESC");
    }
    
    
    if($result)
    {
        echo  success($result,200, "Successful","Successful");
    }else {
        
        echo badRequest(204,'No Package Found!');
    }
}