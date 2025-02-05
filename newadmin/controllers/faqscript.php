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

$question = ((isset($_POST["question"]) && $_POST["question"] !== "") ? htmlentities($_POST["question"],ENT_QUOTES) : "-" );
$answer = ((isset($_POST["answer"]) && $_POST["answer"] !== "") ? htmlentities($_POST["answer"],ENT_QUOTES) : "-" );
if($question === "-" || $answer === "-"){
    exit(badRequest(204, "Not successful. Invalid question or answer"));
}

$framework = new FrameWork();
$res = $framework->getFAQInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        owner = '" . ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? $_POST["owner"] : $res['owner'] ) . "',
        question = '" . ((isset($_POST["question"]) && $_POST["question"] !== "") ? htmlentities($_POST["question"],ENT_QUOTES) : $res['question'] ) . "',
        answer = '" . ((isset($_POST["answer"]) && $_POST["answer"] !== "") ? htmlentities($_POST["answer"],ENT_QUOTES) : $res['answer'] ) . "'";

    $result = $framework->updateFAQDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful");
    }
    
}else{
    
    // fields names
    $fields = "question,answer,owner,status,tlog";
    $values = "
    '" . ((isset($_POST["question"]) && $_POST["question"] !== "") ? htmlentities($_POST["question"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["answer"]) && $_POST["answer"] !== "") ? htmlentities($_POST["answer"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? $_POST["owner"] : "TRAVELS AND TOURS" ) . "',
    '" . ((isset($_POST["status"]) && $_POST["status"] !== "") ? $_POST["status"] : "DISPLAY" ) . "',
    '" . $_SESSION["elfuseremail"] . " | " . date("Y-m-d H:i:s") . "'";

    $result = $framework->registerFAQ($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}
