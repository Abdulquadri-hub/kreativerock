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

$user = new User();
//$result = $user->retrieveByQuerySelector("SELECT * FROM users WHERE class NOT LIKE 'SUPERADMIN'");
$result = $user->retrieveByQuerySelector("SELECT * FROM users");
$userarray = array();
if($result){
    foreach($result as $auser){
        $userarray[] = array(
            "id" => $auser["id"],
            "email" => $auser["email"],
            "firstname" => $auser["firstname"],
            "lastname" => $auser["lastname"],
            "status" => $auser["status"],
            "address" => $auser["address"],
            "class" => $auser["class"],
            "role" => $auser["role"],
            "dateofbirth" => $auser["dateofbirth"],
            "referralcode" => $auser["referralcode"]
            );
    }
    
	echo success($userarray,200, "Successful","Successful");
}else{
	echo badRequest(204,'There may be no users with matching criteria');
}
?>