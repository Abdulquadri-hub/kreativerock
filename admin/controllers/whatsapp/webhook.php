<?php

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$twoWayWhatsApp = new TwoWayWhatsApp();

$rawData = file_get_contents('php://input');
$webhookData = json_decode($rawData, true);

file_put_contents('webhook_requests.log', 
    date('Y-m-d H:i:s') . " - Received webhook: " . $rawData . "\n", 
    FILE_APPEND
);

try {
    $result = $twoWayWhatsApp->handleWebhook($webhookData);
    
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $result]);
} catch (Exception $e) {
    file_put_contents('webhook_errors.log', date('Y-m-d H:i:s') . " - Error processing webhook: " . $e->getMessage() . "\n", FILE_APPEND);
    
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error processing webhook']);
}