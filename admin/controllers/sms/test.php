<?php

session_start();

header('Content-Type: application/json');

$rootFolder = $rootFolder = $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/";
require_once $rootFolder . 'utils/errorhandler.php';
require_once $rootFolder . 'utils/response.php';
require_once $rootFolder . 'model/dbclass.php';
require_once $rootFolder . 'model/model.php';
require_once $rootFolder . 'model/dbFunctions.php';
require_once $rootFolder . 'model/user.php';
require_once $rootFolder . 'model/Contact.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$contactImporter = new Contact();

$email = $_SESSION["elfuseremail"] ?? null;

$type = isset($_REQUEST['type']) && $_REQUEST['type'] !== "" ? strip_tags($_REQUEST['type']) : "";
$textInput = isset($_REQUEST['text']) && $_REQUEST['text'] !== "" ? $_REQUEST['text'] : "";
$fileInput = isset($_FILES['file']) && $_FILES['file'] !== "" ? $_FILES['file'] : "";

//contacts data

$firstname = isset($_REQUEST['firstname']) && $_REQUEST['lastname'] !== "" ? strip_tags($_REQUEST['firstname']) : "";
$lastname = isset($_REQUEST['lastname']) && $_REQUEST['lastname'] !== "" ? strip_tags($_REQUEST['lastname']) : "";
$contactEmail = isset($_REQUEST['email']) && $_REQUEST['email'] !== "" ? strip_tags($_REQUEST['email']) : "";
$sms = isset($_REQUEST['sms']) && $_REQUEST['sms'] !== "" ? $_REQUEST['sms'] : "";
$whatsapp = isset($_REQUEST['whatsapp']) && $_REQUEST['whatsapp'] !== "" ? $_REQUEST['whatsapp'] : "";

if($type === ""){
    echo json_encode(['status' => false, 'message' => 'type is required.']);
    exit;
}


if($type == "text"){
    if($textInput === ""){
        echo json_encode(['status' => false, 'message' => 'text is required.']);
        exit;
    }

    $result =  $contactImporter->importFromText($textInput, $email);
    echo json_encode($result);
    
}elseif($type == "file"){
    if($fileInput === ""){
        echo json_encode(['status' => false, 'message' => 'file is required.']);
        exit;
    }   
    
    $result =  $contactImporter->importFromFile($fileInput, $email);
    echo json_encode($result); 
    
}elseif($type = "form"){

    $contactData = [
        "firstname" => $firstname,
        "lastname" => $lastname,
        "email" => $contactEmail,
        "sms" => $sms,
        "whatsapp" => $whatsapp,
        "landline_number" => $sms
    ];
    
    $result =  $contactImporter->createFromData($contactData, $email);
    echo json_encode($result); 
}




