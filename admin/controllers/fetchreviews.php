<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if($_SESSION["elfuseremail"] === null || $_SESSION["elfuseremail"] === ""){
    //exit(badRequest(204,'Invalid session data. Proceed to login'));
}
$department = new Administration();
//$location = $_SESSION["location_id"];
$startdate = (isset($_POST["startdate"]) && $_POST["startdate"] !== "") ? $_POST["startdate"] : date('Y-m-d');
$enddate = (isset($_POST["enddate"]) && $_POST["enddate"] !== "") ? $_POST["enddate"] : date('Y-m-d');
$status = (isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "%";
$id = (isset($_POST["id"]) && $_POST["id"] > 0) ? $_POST["id"] : 0;
$productid = (isset($_POST["productid"]) && $_POST["productid"] > 0) ? $_POST["productid"] : 0;


if($id == 0){
    if($productid == 0){
        $result = $department->executeByQuerySelector("SELECT * FROM reviews WHERE status LIKE '$status' ORDER BY created_at DESC");
    }else{
        $result = $department->executeByQuerySelector("SELECT * FROM reviews WHERE status LIKE '$status' AND productid = $productid ORDER BY created_at DESC");
    }
}else{
    $result = $department->executeByQuerySelector("SELECT * FROM reviews WHERE id = $id");
}
if($result){
	echo success($result,200, "Successful" ,"Successful");
}else{
	echo badRequest(204, "Not successful"); 
}
?>