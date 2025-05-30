<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$whatsPackage = new WhatsAppPackage();

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
    
    $result = $whatsPackage->registerWhatsappPackage($fields, $values);
    if($result){
        $savedData = $whatsPackage->getWhatsAppPackageInfo("packagename = '$packagename'");
        echo  success($savedData,200, "Successful");
    }else {
        echo badRequest(204, "Registration not successful");
    }
        
}else {
    
    echo badRequest(204, "User Not Found!");
}