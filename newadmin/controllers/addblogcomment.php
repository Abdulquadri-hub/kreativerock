<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';
date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$comment = ((isset($_POST["comment"]) && $_POST["comment"] !== "") ? htmlentities($_POST["comment"],ENT_QUOTES) : "-" );
$blogid = ((isset($_POST["blogid"]) && $_POST["blogid"] > 0) ? intval($_POST["blogid"]) : 0 );

if($blogid == 0 || $comment === "-"){
    exit(badRequest(204, "Not successful. Invalid blogid or comment"));
}

$framework = new FrameWork();
$res = $framework->getBlogCommentInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        comment = '" . (isset($comment) ? $comment : $res['comment'] ) . "',
        blogid = " . ((isset($_POST["blogid"]) && $_POST["blogid"] > 0) ? intval($_POST["blogid"]) : $res['blogid'] );

    $result = $framework->updateBlogCommentDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful");
    }
    
}else{
    
    // fields names
    $fields = "blogid,comment,status";
    $values = "$blogid,'$comment','OPEN'";

    $result = $framework->registerBlogComment($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}
