<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$created_at = date('Y-m-d H:i:s');
$admin = new Administration();
//$res = $department->getDepartmentInfo("id = " . intval($_POST['id']));

// fields names
$fields = "productid,email,fullname,rating,comment,module,status,created_at";

// values for requests
$values = "
" . ((isset($_REQUEST["productid"]) && intval($_REQUEST["productid"]) > 0) ? intval($_REQUEST["productid"]) : 0) . ",
'".(isset($_REQUEST["email"]) && $_REQUEST["email"] !== "" ? htmlentities(strip_tags( $_REQUEST["email"])) : "-")."',
'".(isset($_REQUEST["fullname"]) && $_REQUEST["fullname"] !== "" ? htmlentities(strip_tags( $_REQUEST["fullname"])) : "")."',
" . ((isset($_REQUEST["rating"]) && intval($_REQUEST["rating"]) > 0) ? intval($_REQUEST["rating"]) : 0) . ",
'".(isset($_REQUEST["comment"]) && $_REQUEST["comment"] !== "" ? htmlentities($_REQUEST["comment"],ENT_QUOTES) : "-")."',
'" . ((isset($_REQUEST["module"]) && intval($_REQUEST["module"]) !== "") ? $_REQUEST["module"] : 'TOURS') . "',
'PENDING',
'$created_at'";

$result = $admin->registerReview($fields, $values);

if ($result) {
    // echo 200 OK status 
    echo success($result, 200, "Successful", "Successful");
}else {
    //echo 204 failed status
    exit(badRequest(204,'Not Successful'));
}

