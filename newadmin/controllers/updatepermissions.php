<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
date_default_timezone_set('Africa/Lagos');

if($_SESSION["elfuseremail"] === null || $_SESSION["elfuseremail"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    exit(json_encode($response));
}

$resp = array("status" => false,"code" => "204","message"=>"");
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "-";
if( $email === "-"){
    $resp["message"] = "user is invalid";
    //$user->addUserActivity($phone, "Failed Attempt to change pin at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CHANGE PIN");
    exit(json_encode($resp));
}
$permissions = ((isset($_POST["permissions"]) && $_POST["permissions"] !== "") ? $_POST["permissions"] : "-");
$role = ((isset($_POST["role"]) && $_POST["role"] !== "") ? $_POST["role"] : "USER");
$user = new User();
$resuser = $user->getUserInfo("email = '" . $email . "'");
if($_SESSION["role"] === "SUPERADMIN"){
    if($resuser){
        if($permissions !== "-"){
            $resultupdate = $user->updateUserDetails("role='" . $role . "',permissions='" . $permissions . "'", $resuser["id"]);
            if($resultupdate){
                $resp["message"] = "Successful";
                exit(json_encode($resp));
            }
        }else{
            $resp["message"] = "Bad permission data";
            exit(json_encode($resp));
        }
    }else{
        $resp["message"] = "We could not find this user";
        exit(json_encode($resp));
    }
}else{
    exit(badRequest(204,'This page is not available to you'));
}   

?>