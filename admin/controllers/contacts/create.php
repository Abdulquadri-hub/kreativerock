<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$contactImporter = new Contact();

$email = $_SESSION["elfuseremail"] ??  null;
$type = isset($_REQUEST['type']) && $_REQUEST['type'] !== "" ? strip_tags($_REQUEST['type']) : "";

//contacts data for form type
$firstname = isset($_REQUEST['firstname']) && $_REQUEST['lastname'] !== "" ? strip_tags($_REQUEST['firstname']) : "";
$lastname = isset($_REQUEST['lastname']) && $_REQUEST['lastname'] !== "" ? strip_tags($_REQUEST['lastname']) : "";
$contactEmail = isset($_REQUEST['email']) && $_REQUEST['email'] !== "" ? strip_tags($_REQUEST['email']) : "";
$sms = isset($_REQUEST['sms']) && $_REQUEST['sms'] !== "" ? $_REQUEST['sms'] : "";
$whatsapp = isset($_REQUEST['whatsapp']) && $_REQUEST['whatsapp'] !== "" ? $_REQUEST['whatsapp'] : "";
$segment_id = isset($_REQUEST['segment_id']) && $_REQUEST['segment_id'] !== "" ? $_REQUEST['segment_id'] : null;

if($type == "form"){

    $contactData = [
        "firstname" => $firstname,
        "lastname" => $lastname,
        "email" => $contactEmail,
        "sms" => $sms,
        "whatsapp" => $whatsapp,
        "landline_number" => $sms,
        "segment_id" => $segment_id
    ];
    
    $result =  $contactImporter->createFromData($contactData, $email);
    echo json_encode($result); 
    
}elseif($type == ""){

    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        exit(badRequest(204,'Invalid JSON data'));
    }
    
    if($data['type'] == "text"){
        $result =  $contactImporter->importFromText($data, $email);
        echo json_encode($result);
        
    }
    else {
        $result =  $contactImporter->importFromFile($data, $email);
        echo json_encode($result); 
    }
}




