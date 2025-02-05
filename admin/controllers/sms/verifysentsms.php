<?php

session_start();

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/DotgoApi.php';
require_once $rootFolder . 'utils/sanitize.php';

$dotgo = new DotgoApi();
$errors = [];

$msgId = (isset($_REQUEST['msg_id']) && $_REQUEST['msg_id'] !== "") ?  $_REQUEST['msg_id'] : $errors['msg_id'] = "Message Id Is Required";
if($msgId == "")
{
    exit(json_encode(["errors" => $errors]));
}
print_r($_REQUEST);
$result =  $dotgo->checkDeliveryStatus($msgId);
echo json_encode($result);

