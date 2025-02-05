<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/Administration.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$role = $_SESSION["role"];
$email = "%"; //isset($_SESSION["selectedemail"]) && $_SESSION["selectedemail"] !== "") ? $_SESSION["selectedemail"] : $_SESSION['elfuseremail'];
$admin = new Administration();

$startdate = (isset($_POST["startdate"]) && $_POST["startdate"] !== "") ? $_POST["startdate"] : date('Y-m-d');
$enddate = (isset($_POST["enddate"]) && $_POST["enddate"] !== "") ? $_POST["enddate"] : date('Y-m-d');
$status = (isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "%";
$email = (isset($_POST["email"]) && $_POST["email"] !== "") ? $_POST["email"] : "%";
//$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

if($role === "MERCHANT" || $role === "USER"){
    $email = $_SESSION['elfuseremail'];
    $result = $admin->executeByQuerySelector("SELECT * FROM transactions WHERE user LIKE '$email' AND status LIKE '$status' AND DATE(transactiondate) BETWEEN '$startdate' AND '$enddate' ORDER BY transactiondate DESC");
}else{
    $email = "%";
    $result = $admin->executeByQuerySelector("SELECT * FROM transactions WHERE user LIKE '$email' AND status LIKE '$status' AND DATE(transactiondate) BETWEEN '$startdate' AND '$enddate' ORDER BY transactiondate DESC");
}
if($result){
	echo success($result,200, "Successful","Successful");
}else{
	echo badRequest(204,'Not successful');
}
?>
