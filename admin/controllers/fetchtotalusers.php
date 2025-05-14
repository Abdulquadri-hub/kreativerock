<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
//$result = $user->retrieveByQuerySelector("SELECT * FROM users WHERE class NOT LIKE 'SUPERADMIN'");
$result = $user->retrieveByQuerySelector("SELECT count(*) AS registeredusers FROM users");
$userarray = array();
if($result){
    $userarray = array(
        "registeredusers" => $result[0]["registeredusers"]
        );
    
	echo success($userarray,200, "Successful","Successful");
}else{
    $userarray = array(
        "registeredusers" => 0
        );    
	echo success($userarray,200, "Successful","Successful");
}
?>