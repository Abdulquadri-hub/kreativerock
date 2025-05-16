<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$smsPackage = new SmsPackage();

$email = $_SESSION["elfuseremail"] ??  null;
$res = $user->getUserInfo("email = '" . $email . "'");

if($res)
{
   
    $id = isset($_REQUEST['id'])  && $_REQUEST['id'] ? $_REQUEST['id'] :  "";
    if($id === "")
    {
        exit("BAD REQUEST");
    }

    if($smsPackage->checkIfSmsPackageExists("id = '" . $id . "'"))
    {
        
        $result = $smsPackage->removeSmsPackage("id = '" . $id . "'");

        if($result)
        {
	     
            echo  success($result,200, "Successful","Successful");
            
        }else {
            
            echo badRequest(204, "Delete not successful");
        }
        
    }else {
        
        echo badRequest(204, "Package Do not Exists");
    }
    
    
}else {
    
    echo badRequest(204, "User Not Found!");
}