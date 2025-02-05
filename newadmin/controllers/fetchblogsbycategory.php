<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$framework = new FrameWork();
$categoryid = (isset($_POST["categoryid"]) && intval($_POST["categoryid"]) > 0) ? $_POST["categoryid"] : 0;

if($categoryid > 0){
    $results = $framework->executeByQuerySelector("SELECT *,(SELECT category FROM blogcategories WHERE blogcategories.id = blogpost.categoryid) AS category FROM blogpost WHERE categoryid = $categoryid");
    if($results){
        $dataarray = array();
        foreach($results as $result){
            $dataarray[] = array(
                "id" => $result["id"],
                "categoryid" => $result["categoryid"],
                "title" => html_entity_decode($result["title"],ENT_QUOTES),
                "subtitle" => html_entity_decode($result["subtitle"],ENT_QUOTES),
                "content" => html_entity_decode($result["content"],ENT_QUOTES),
                "category" => html_entity_decode($result["category"],ENT_QUOTES),
                "status" => $result["status"],
                "imageurl" => $result["imageurl"],
                "tlog" => $result["tlog"]
                );
        }
	    echo success($dataarray,200, "Successful","Successful");        
    }else{
    	exit(badRequest(204,'Not successful. No data'));
    }    
}else{
	exit(badRequest(204,'Not successful.'));
} 

?>