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

$email = (isset($_SESSION["selectedemail"]) && $_SESSION["selectedemail"] !== "") ? $_SESSION["selectedemail"] : $_SESSION['elfuseremail'];
$admin = new Administration();

$ref = (isset($_POST["ref"]) && $_POST["ref"] !== "") ? $_POST["ref"] : "BAD";
$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

if($ref === "BAD"){
    exit(badRequest(204,'Bad ref'));
}
if($_SESSION['class'] === "SUPERADMIN"){
    $result = $admin->executeByQuerySelector("UPDATE ticket SET status = 'CLOSED' WHERE ref = '$ref'");
}else{
    exit(badRequest(204,'Operation not permitted'));
}
if($result){
	echo success($result,200, "Successful","Successful");
}else{
	echo badRequest(204,'Not successful');
}
?>
