<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/Administration.php';

date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}
$ref = ((isset($_POST["ref"]) && $_POST["ref"] !== "") ? htmlentities($_POST["ref"],ENT_QUOTES) : "-" );
if($ref === "-"){
    exit(badRequest(204, "Ref is not valid"));
}
$admin = new Administration();
$res = $admin->executeByQuerySelector("SELECT * FROM ticket WHERE ref = '$ref' limit 1");

if($res){
    $title = $res['title'];

    // fields names
    $fields = "email,title,message,entrydate,ref,status,tlog";
    $values = "
    '" . $_SESSION['elfuseremail'] . "',
    '" . $title . "',
    '" . ((isset($_POST["message"]) && $_POST["message"] !== "") ? htmlentities($_POST["message"],ENT_QUOTES) : "-" ) . "',
    '" . date('Y-m-d H:i:s') . "',
    '$ref',
    'OPEN',
    '" . $_SESSION["elfuseremail"] . " | " . date("Y-m-d H:i:s") . "'";

    $result = $admin->registerTicket($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}else{
    echo badRequest(204, "Not successful. There is match for the ref.");
}
