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

$category = ((isset($_POST["category"]) && $_POST["category"] !== "") ? htmlentities($_POST["category"],ENT_QUOTES) : "-" );
//$answer = ((isset($_POST["answer"]) && $_POST["answer"] !== "") ? htmlentities($_POST["answer"],ENT_QUOTES) : "-" );
if($category === "-"){
    exit(badRequest(204, "Not successful. Invalid category"));
}

$framework = new FrameWork();
$res = $framework->getBlogCategoriesInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        category = '" . $category . "'";

    $result = $framework->updateBlogCategoriesDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful");
    }
    
}else{
    
    // fields names
    $fields = "category,tlog";
    $values = "
    '" . $category . "',
    '" . $_SESSION["elfuseremail"] . " | " . date("Y-m-d H:i:s") . "'";

    $result = $framework->registerBlogCategories($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}
