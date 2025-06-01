<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$whatsPackage = new WhatsAppPackage();

$email = $_SESSION["elfuseremail"] ??  null;
$res = $user->getUserInfo("email = '" . $email . "'");

if($res)
{
   
    $id = isset($_REQUEST['id'])  && $_REQUEST['id'] ? $_REQUEST['id'] :  "";
    if($id === "")
    {
        exit(badRequest(400, "id is required"));
    }

    if($whatsPackage->checkIfWhatsAppPackageExists("id = '" . $id . "'"))
    {
        
        $result = $whatsPackage->removeWhatsAppPackage("id = '" . $id . "'");

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