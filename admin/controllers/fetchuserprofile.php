<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";


if(isset($_SESSION["user_id"]) && $_SESSION["user_id"] === null || $_SESSION["user_id"] === "" || $_SESSION["elfuseremail"] === null || $_SESSION["elfuseremail"] === ""){
    $response = array("status" => false,"code" => 204,"message" => "Session expired. Proceed to login");
    exit(json_encode($response));
}
$email = isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : $_SESSION["elfuseremail"];


$user = new User();
$res = $user->getUserInfo("email = '" . $email . "'");
if($res){
    $result = array(
        "status" => true,
        "code" => 200,
        "message" => "Successful",
        "firstname" => $res["firstname"],
        "lastname" => $res["lastname"],
        "othernames" => $res["othernames"],
        "phone" => $res["phone"],
        "address" => $res["address"],
        "email" => $res["email"],
        "class" => $res["class"],
        "permissions" => $res["permissions"],
        "tlog" => $res["tlog"],
        "role" => $res["role"],
        "imageurl" => $res["imageurl"],
        "identificationtype" => $res["identificationtype"],
        "dateofbirth" => $res["dateofbirth"],
        "online" => $res["online"],
        "permissions" => $res["permissions"],
        "referralcode" => $res["referralcode"],
        "referredby" => $res["referredby"],
        "rowstatus" => $res["status"]
        
    );

    echo(json_encode($result));
}else{
    $res = array("status" => false,"code" => 204,"message" => "Not Successful");
    exit(json_encode($res));
}
        

?>

