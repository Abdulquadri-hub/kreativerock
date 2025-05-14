<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$framework = new FrameWork();
$results = $framework->executeByQuerySelector("SELECT * FROM markup");
if($results){
    echo success($results, 200, "Successful", "Successful");
}else{
    echo badRequest(204, "Not successful");
}
//$jresult = json_encode($results);

?>