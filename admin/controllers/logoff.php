<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';

$user = new User();
$user->updateUserDetails("online='NO'", $_SESSION["user_id"]); //set the online status

$_SESSION["elfuseremail"] = null;
$_SESSION["user_id"] = null;
$_SESSION["role"] = null;
//$_SESSION["location_id"] = null;
session_unset();
session_destroy();

header('Location: ../../home/');

?>
