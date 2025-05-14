<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$framework = new FrameWork();
$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;

if($id == 0){
    $results = $framework->executeByQuerySelector("SELECT *,(SELECT category FROM blogcategories WHERE blogcategories.id = blogpost.categoryid) AS category FROM blogpost");
}else{
    $results = $framework->executeByQuerySelector("SELECT *,(SELECT category FROM blogcategories WHERE blogcategories.id = blogpost.categoryid) AS category FROM blogpost WHERE id = $id");
}
if($results){
    $dataarray = array();
    foreach($results as $result){
        $resultcomment = $framework->executeByQuerySelector("SELECT * FROM blogcomment WHERE blogid = " . $result["id"]);
        $dataarray[] = array(
            "id" => $result["id"],
            "categoryid" => $result["categoryid"],
            "title" => html_entity_decode($result["title"],ENT_QUOTES),
            "subtitle" => html_entity_decode($result["subtitle"],ENT_QUOTES),
            "content" => html_entity_decode($result["content"],ENT_QUOTES),
            "category" => html_entity_decode($result["category"],ENT_QUOTES),
            "status" => $result["status"],
            "imageurl" => $result["imageurl"],
            "tlog" => $result["tlog"],
            "comments" => $resultcomment
            );
    }
	echo success($dataarray,200, "Successful","Successful");
}else{
	exit(badRequest(204,'Not successful'));
}
?>