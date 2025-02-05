<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$currencycode = ((isset($_POST["currencycode"]) && $_POST["currencycode"] !== "") ? htmlentities($_POST["currencycode"],ENT_QUOTES) : "-" );

if($currencycode === "-"){
    exit(badRequest(204, "Not successful. Invalid currency code"));
}

$framework = new FrameWork();
$res = $framework->getMarkupInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        currencycode = '" . ((isset($currencycode) && $currencycode !== "") ? $currencycode : $res['currencycode']) . "',
        markupforusd = " . ((isset($_POST["markupforusd"]) && $_POST["markupforusd"] > 0) ? $_POST["markupforusd"] : $res['markupforusd']) . ",
        markupforeur = " . ((isset($_POST["markupforeur"]) && $_POST["markupforeur"] > 0) ? $_POST["markupforeur"] : $res['markupforeur']) . ",
        markupforgbp = " . ((isset($_POST["markupforgbp"]) && $_POST["markupforgbp"] > 0) ? $_POST["markupforgbp"] : $res['markupforgbp']);

    $result = $framework->updateMarkupDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful");
    }
    
}else{
    
    // fields names
    $fields = "currencycode,markupforusd,markupforeur,markupforgbp,status";
    $values = "'" . $currencycode . "',
    " . ((isset($_POST["markupforusd"]) && $_POST["markupforusd"] > 0) ? $_POST["markupforusd"] : 0) . ",
    " . ((isset($_POST["markupforeur"]) && $_POST["markupforeur"] > 0) ? $_POST["markupforeur"] : 0) . ",
    " . ((isset($_POST["markupforgbp"]) && $_POST["markupforgbp"] > 0) ? $_POST["markupforgbp"] : 0) . ",
    'OPEN'";

    $result = $framework->registerMarkup($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}
