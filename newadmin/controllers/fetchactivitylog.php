<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';

date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$startdate = (isset($_POST["startdate"]) && $_POST["startdate"] !== "") ? $_POST["startdate"] : date('Y-m-d');
$enddate = (isset($_POST["enddate"]) && $_POST["enddate"] !== "") ? $_POST["enddate"] : date('Y-m-d');
$email = (isset($_POST["email"]) && $_POST["email"] !== "") ? $_POST["email"] : '%';

$user = new User();
//$result = $user->retrieveByQuerySelector("SELECT * FROM users WHERE class NOT LIKE 'SUPERADMIN'");
$result = $user->retrieveByQuerySelector("SELECT * FROM user_activity WHERE username LIKE '$email' AND DATE(currenttime) BETWEEN '$startdate' AND '$enddate'");
$userarray = array();
if($result){
	echo success($result,200, "Successful","Successful");
}else{
    exit(badRequest(204,'Not successful'));
}
?>