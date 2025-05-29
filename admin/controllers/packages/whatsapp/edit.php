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
   
    $id = isset($_REQUEST['id'])  && $_REQUEST['id'] ? $_REQUEST['id'] :  "";
    if($id === ""){
        exit(badRequest(400, "id is required"));
    }

    if($whatsPackage->checkIfWhatsAppPackageExists("id = '$id'")){

        $row = $whatsPackage->getWhatsAppPackageInfo("id = '$id'");

        if(!empty($row)){
            $status = "Active";
            $query =  " packagename =  '" . ((isset($_REQUEST["packagename"]) && $_REQUEST["packagename"] !== '') ? htmlentities($_REQUEST["packagename"],ENT_QUOTES) : $row["packagename"]) . "', 
                        numberofunits =  '" . ((isset($_REQUEST["numberofunits"]) && $_REQUEST["numberofunits"] !== '') ? htmlentities($_REQUEST["numberofunits"],ENT_QUOTES) : $row["numberofunits"]) . "', 
                        costperunit =  '" . ((isset($_REQUEST["costperunit"]) && $_REQUEST["costperunit"] !== '') ? htmlentities($_REQUEST["costperunit"],ENT_QUOTES) : $row["costperunit"]) . "', 
                        status =  '" . $status . "'
                      " ;
	        
	        $result = $whatsPackage->updateWhatsAppPackageDetails($query, $id);
	        if($result){
                $updatedRow = $whatsPackage->getWhatsAppPackageInfo("id = '$id'");
                echo success($result,200, "Successful",$updatedRow);
            }else {
                echo badRequest(204, "Update not successful");
            }
            
        }
        
    }else {
        echo badRequest(204, "Package do not Exists");
    }
    
    
}else {
    
    echo badRequest(204, "User Not Found!");
}