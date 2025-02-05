<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     //exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$framework = new FrameWork();
//$id = (isset($_POST["id"]) && intval($_POST["id"]) > 0) ? $_POST["id"] : 0;
$searchtext = (isset($_POST["searchtext"]) && intval($_POST["searchtext"]) !== "") ? $_POST["searchtext"] . "%" : "%";



$results = $framework->executeByQuerySelector("SELECT * FROM organisation WHERE organisationname LIKE '$searchtext'");
  
if($results){
    $dataarray = array();
    foreach($results as $result){
        $dataarray[] = array(
            "id" => $result["id"],
            "owner" => $result["owner"],
            "organisationname" => html_entity_decode($result["organisationname"],ENT_QUOTES),
            "phone" => html_entity_decode($result["phone"],ENT_QUOTES),
            "address" => html_entity_decode($result["address"],ENT_QUOTES),
            "email" => html_entity_decode($result["email"],ENT_QUOTES),
            "contactperson" => html_entity_decode($result["contactperson"],ENT_QUOTES),
            "contactpersonemail" => html_entity_decode($result["contactpersonemail"],ENT_QUOTES),
            "facebook" => html_entity_decode($result["facebook"],ENT_QUOTES),
            "xlink" => html_entity_decode($result["xlink"],ENT_QUOTES),
            "instagram" => html_entity_decode($result["instagram"],ENT_QUOTES),
            "youtube" => html_entity_decode($result["youtube"],ENT_QUOTES),
            "linkedin" => html_entity_decode($result["linkedin"],ENT_QUOTES),
            "state" => html_entity_decode($result["state"],ENT_QUOTES),
            "country" => html_entity_decode($result["country"],ENT_QUOTES),
            "currency" => html_entity_decode($result["currency"],ENT_QUOTES),
            "status" => $result["status"],
            "logo" => $result["logo"],
            "tlog" => $result["tlog"]
            );
    }
    echo success($dataarray,200, "Successful","Successful");        
}else{
	exit(badRequest(204,'Not successful. No data'));
}    


?>