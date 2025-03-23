<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();
$contactImporter = new Contact();

$_REQUEST['email'] = $_SESSION["elfuseremail"] ?? null;
$result = $contactImporter->getContacts($_REQUEST);
if(!$result){
   echo json_encode(['status' => false, 'message' => 'Contacts not found.', 'data' => []]); 
   exit;
}

echo json_encode(['status' => true, 'message' => 'Contacts fetched successfully.', 'data' => $result]);
    


