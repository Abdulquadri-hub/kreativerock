<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$framework = new FrameWork();
$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

if($id == 0){
    $result = $framework->executeByQuerySelector("SELECT * FROM blogcategories");
}else{
    $result = $framework->executeByQuerySelector("SELECT * FROM blogcategories WHERE id = $id");
}
if($result){
	echo success($result,200, "Successful","Successful");
}else{
	exit(badRequest(204,'Not successful'));
}
?>