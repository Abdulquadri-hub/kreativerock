<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$framework = new FrameWork();

$status = (isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "%";
$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

if($id == 0){
    $result = $framework->executeByQuerySelector("SELECT * FROM faqs WHERE status LIKE '$status'");
}else{
    $result = $framework->executeByQuerySelector("SELECT * FROM faqs WHERE status LIKE '$status' AND id = $id");
}
if($result){
	echo success($result,200, "Successful","Successful");
}else{
	echo success($result,204, "Failed","ERROR"); 
}
?>