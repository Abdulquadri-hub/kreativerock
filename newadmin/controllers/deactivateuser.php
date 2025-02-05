<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

//$email = (isset($_POST["email"]) && $_POST["email"]) ? $_POST["email"] : "BAD";
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
$user = new User();

if($email === "BAD"){
    exit(badRequest(204,'Bad email'));
}else{
    $result = $user->retrieveByQuerySelector("UPDATE users SET status = 'DEACTIVATED' WHERE class NOT LIKE 'SUPERADMIN'");
    if($result){
        echo success($_SESSION["selecteduser"],200, "Successful","Successful");
    }else{
        exit(badRequest(204,'Not successful'));
    }
}
	
?>