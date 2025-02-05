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

if($_FILES["userphotoname"]["name"] !== "" && $_FILES["userphotoname"]["name"] !== null && $_FILES["userphotoname"]["name"] !== "-"){
	$filenamearray = explode(".",$_FILES["userphotoname"]["name"]); 
	$filename1 = $filenamearray[0] . mt_rand(10,111111111) . "." . end($filenamearray);
}else { $filename1 = "-"; }

$returnvalue = '';
if($_POST["photofilename"] !== null && $_POST["photofilename"] !== "-"){	
	$allowedExts = array("gif", "jpeg", "jpg","pdf","png");
	$temp = explode(".", $_FILES["userphotoname"]["name"]);
	$returnvalue .= '' . $_FILES["userphotoname"]["name"];
	$extension = end($temp);
	if ((($_FILES["userphotoname"]["type"] === "image/gif") || ($_FILES["userphotoname"]["type"] === "image/jpeg") || ($_FILES["userphotoname"]["type"] === "image/jpg") || ($_FILES["userphotoname"]["type"] === "image/pjpeg") || ($_FILES["userphotoname"]["type"] === "image/png") || ($_FILES["userphotoname"]["type"] === "application/pdf")) && in_array($extension, $allowedExts)){
		if ($_FILES["userphotoname"]["error"] > 0){
			$returnvalue .= ' | error saving photo'; //echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
		}else{
			/*
			if (file_exists("../images/borrowers/" . $_FILES["userphotoname"]["name"])){
				$returnvalue .= ' | the file exists'; //echo $_FILES["userphoto"]["name"] . " already exists. ";
  		    }else{ */
				move_uploaded_file($_FILES["userphotoname"]["tmp_name"],"../images/" . $filename1);
				$returnvalue .= "Upload Successful";
  		    //}
		}
	}else{
         $returnvalue .= ' | Invalid file type';	    
	}
}

$title = ((isset($_POST["title"]) && $_POST["title"] !== "") ? htmlentities($_POST["title"],ENT_QUOTES) : "-" );
$subtitle = ((isset($_POST["subtitle"]) && $_POST["subtitle"] !== "") ? htmlentities($_POST["subtitle"],ENT_QUOTES) : "-" );
$content = ((isset($_POST["content"]) && $_POST["content"] !== "") ? htmlentities($_POST["content"],ENT_QUOTES) : "-" );
$categoryid = ((isset($_POST["categoryid"]) && $_POST["categoryid"] > 0) ? intval($_POST["categoryid"]) : 0 );

if($title === "-" || $content === "-"){
    exit(badRequest(204, "Not successful. Invalid title or content"));
}
if($categoryid == 0){
    exit(badRequest(204, "Not successful. Invalid categoryid"));
}
$framework = new FrameWork();
$res = $framework->getBlogInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        title = '" . ((isset($_POST["title"]) && $_POST["title"] !== "") ? htmlentities($_POST["title"],ENT_QUOTES) : $res['title'] ) . "',
        subtitle = '" . ((isset($_POST["subtitle"]) && $_POST["subtitle"] !== "") ? htmlentities($_POST["subtitle"],ENT_QUOTES) : $res['subtitle'] ) . "',
        categoryid = " . ((isset($_POST["categoryid"]) && $_POST["categoryid"] > 0) ? intval($_POST["categoryid"]) : $res['categoryid'] ) . ",
        imageurl = '" . ((isset($filename1) && $filename1 !== "-") ? $filename1 : $res['imageurl'] ) . "',
        content = '" . ((isset($_POST["content"]) && $_POST["content"] !== "") ? htmlentities($_POST["content"],ENT_QUOTES) : $res['content'] ) . "'";

    $result = $framework->updateBlogDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful");
    }
    
}else{
    
    // fields names
    $fields = "title,subtitle,categoryid,content,imageurl,status,tlog";
    $values = "
    '" . ((isset($_POST["title"]) && $_POST["title"] !== "") ? htmlentities($_POST["title"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["subtitle"]) && $_POST["subtitle"] !== "") ? htmlentities($_POST["subtitle"],ENT_QUOTES) : "-" ) . "',
    " . ((isset($_POST["categoryid"]) && $_POST["categoryid"] > 0) ? intval($_POST["categoryid"]) : -1 ) . ",
    '" . ((isset($_POST["content"]) && $_POST["content"] !== "") ? htmlentities($_POST["content"],ENT_QUOTES) : "-" ) . "',
    '$filename1',
    'OPEN',
    '" . $_SESSION["elfuseremail"] . " | " . date("Y-m-d H:i:s") . "'";

    $result = $framework->registerBlog($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}
