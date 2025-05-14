<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

//$email = (isset($_SESSION["selectedemail"]) && $_SESSION["selectedemail"] !== "") ? $_SESSION["selectedemail"] : $_SESSION['elfuseremail'];
$admin = new Administration();

$startdate = (isset($_POST["startdate"]) && $_POST["startdate"] !== "") ? $_POST["startdate"] : date('Y-m-d');
$enddate = (isset($_POST["enddate"]) && $_POST["enddate"] !== "") ? $_POST["enddate"] : date('Y-m-d');
$status = (isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "%";
$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

$result = $admin->executeByQuerySelector("SELECT * FROM transactions WHERE status = '$status'");
if($result){
    $ttotal = array(
        "transactionstotal" => count($result)
        );
    
	echo success($ttotal,200, "Successful","Successful");

}else{
    $ttotal = array(
        "transactionstotal" => 0
        );
    
	echo success($ttotal,200, "Successful","Successful");
}
?>
