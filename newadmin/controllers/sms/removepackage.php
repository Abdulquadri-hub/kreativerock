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

$user = new User();
$smsPackage = new SmsPackage();


$email = $_SESSION["elfuseremail"] ??  null;
// $email = $_SESSION["elfuseremail"] ??  "abdulquadri.aq@gmail.com";
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