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

$status = (isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "%";
$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

if($_SESSION['class'] === "SUPERADMIN"){
    if($id == 0){
        $result = $admin->executeByQuerySelector("SELECT * FROM ticket WHERE status LIKE '$status' GROUP BY ref");
    }else{
        $result = $admin->executeByQuerySelector("SELECT * FROM ticket WHERE status LIKE '$status' AND id = $id GROUP BY ref");
    }
}else{
    if($id == 0){
        $result = $admin->executeByQuerySelector("SELECT * FROM ticket WHERE status LIKE '$status' AND email = '$email' GROUP BY ref");
    }else{
        $result = $admin->executeByQuerySelector("SELECT * FROM ticket WHERE status LIKE '$status' AND id = $id");
    }
}
if($result){
	echo success($result,200, "Successful","Successful");
}else{
	echo badRequest(204,'Not successful');
}
?>
