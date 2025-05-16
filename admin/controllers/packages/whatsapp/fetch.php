<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$whatsPackage = new WhatsAppPackage();

$packageId = isset($_REQUEST['id']) && $_REQUEST['id'] !== ""  ?  $_REQUEST['id'] :  "";
$startDate = isset($_POST['start_date']) && $_POST['start_date'] !== "" ?  $_POST['start_date'] :  "";
$endDate = isset($_POST['end_date']) && $_POST['end_date'] !== "" ?  $_POST['end_date'] :  date("Y-m-d H:i:s");

if(!empty($packageId))
{
    if(!empty($startDate))
    {
        $result =  $whatsPackage->retrieveByQuerySelector("SELECT * FROM whatsapp_packages WHERE id = $packageId AND WHERE created_at BETWEEN $startDate AND $endDate ORDER BY id DESC");
    }else {
        
        $result = $whatsPackage->retrieveByQuerySelector("SELECT * FROM whatsapp_packages WHERE id = $packageId ORDER BY id DESC");
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
        
        $result =  $whatsPackage->retrieveByQuerySelector("SELECT * FROM whatsapp_packages WHERE created_at BETWEEN $startDate AND $endDate ORDER BY id DESC");
    }else {
        
        $result = $whatsPackage->retrieveByQuerySelector("SELECT * FROM whatsapp_packages ORDER BY id DESC");
    }
    
    
    if($result)
    {
        echo  success($result,200, "Successful","Successful");
    }else {
        
        echo badRequest(204,'No Package Found!');
    }
}