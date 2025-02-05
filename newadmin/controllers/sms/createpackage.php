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
require_once $rootFolder . 'utils/sanitize.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}


$user = new User();
$smsPackage = new SmsPackage();

// $email = $_SESSION["elfuseremail"] ??  "abdulquadri.aq@gmail.com";
$email = $_SESSION["elfuseremail"] ??  null;
$res = $user->getUserInfo("email = '" . $email . "'");

if($res)
{
    $user = $res['email'];
    $status = "Active";
    $packagename = (isset($_POST['packagename']) && $_POST['packagename']  !== "") ? $_POST['packagename'] : "" ;
    $numberofunits = (isset($_POST['numberofunits']) && $_POST['numberofunits'] !== "") ? $_POST['numberofunits'] : "";
    $costperunit = (isset($_POST['costperunit']) && $_POST['costperunit'] !== "") ?  $_POST['costperunit'] : "";
    
    if(($packagename == "") || ($numberofunits == "" ) || ($numberofunits == "") ||  ($costperunit == "" || $costperunit < 0))
    {
        exit(badRequest(204, "BAD REQUEST!"));
    }
    
    $fields = "`packagename`, `numberofunits`, `costperunit`, `status`, `user`";
    $values = " 
	            '" . $packagename . "',
	            '" . $numberofunits . "',
	            '" . $costperunit . "',
	            '" . $status . "',
	            '" . $user . "'
	          ";
    
    $result = $smsPackage->registerSmsPackage($fields, $values);
    if($result)
    {
        echo  success($result,200, "Successful","Successful");
        
    }else {
        
        echo badRequest(204, "Registration not successful");
    }
        
}else {
    
    echo badRequest(204, "User Not Found!");
}