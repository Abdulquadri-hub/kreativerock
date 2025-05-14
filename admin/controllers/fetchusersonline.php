<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";
if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
//$result = $user->retrieveByQuerySelector("SELECT * FROM users WHERE class NOT LIKE 'SUPERADMIN'");
$result = $user->retrieveByQuerySelector("SELECT count(*) AS onlineusers FROM users WHERE online = 'YES'");
$userarray = array();
if($result){
    $userarray = array(
        "totalusersonline" => $result[0]["onlineusers"]
        );
    
	echo success($userarray,200, "Successful","Successful");
}else{
    $userarray = array(
        "totalusersonline" => 0
        );    
	echo success($userarray,200, "Successful","Successful");
}
?>