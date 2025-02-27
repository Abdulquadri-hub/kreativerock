<?php
session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

// if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
// }

try {

    $conversation = new Conversation();

    $jsonInput = file_get_contents('php://input');
    $params = json_decode($jsonInput, true);
    
    $requiredFields = ["campaign_id", "phonenumber"];
    
    foreach ($requiredFields as $field) {
        if (!isset($params[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }
    
    $email = $_SESSION["elfuseremail"] ??  "abdulquadri.aq@gmail.com";

    $result = $conversation->getMessageChain($params['campaign_id'], $params['phonenumber']);

    http_response_code(200);
    echo success($result);

} catch (\Exception $e) {

    $code = http_response_code(500);
    exit(error($result, $code, $e->getMessage()));
}







