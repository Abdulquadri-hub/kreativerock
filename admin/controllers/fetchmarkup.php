<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

$framework = new FrameWork();
$results = $framework->executeByQuerySelector("SELECT * FROM markup");
if($results){
    echo success($results, 200, "Successful", "Successful");
}else{
    echo badRequest(204, "Not successful");
}
//$jresult = json_encode($results);

?>