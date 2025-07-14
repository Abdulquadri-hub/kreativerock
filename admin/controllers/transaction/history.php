<?php

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(401, 'Invalid session data. Proceed to login'));
}

try {
    $transactions = new Transactions();
    $userEmail = $_SESSION["elfuseremail"] ?? NULL;
    
    // Determine the action based on request
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'get_transactions';
    
    switch ($action) {
        case 'get_transactions':
            handleGetTransactions($transactions, $userEmail);
            break;
            
        case 'get_stats':
            handleGetStats($transactions, $userEmail);
            break;
            
        default:
            echo badRequest(400, 'Invalid action specified');
            break;
    }
    
} catch (Exception $e) {
    error_log("Super Admin Transactions Error: " . $e->getMessage());
    echo badRequest(500, 'An error occurred while processing your request');
}

function handleGetTransactions($transactions, $userEmail) {
 
    $request = array_merge($_GET, $_POST);
    
    $filters = [
        'type' => validateTransactionType($request['type'] ?? 'all'),
        'status' => trim($request['status'] ?? ''),
        'reference' => trim($request['reference'] ?? ''),
        'user' => trim($request['user'] ?? ''),
        'start_date' => validateDate($request['start_date'] ?? ''),
        'end_date' => validateDate($request['end_date'] ?? ''),
        'limit' => validateLimit($request['limit'] ?? 50),
        'offset' => validateOffset($request['offset'] ?? 0)
    ];
    
    error_log("Super Admin Transaction Request - User: $userEmail, Filters: " . json_encode($filters));
    
    $result = $transactions->getAllTransactions($filters, $userEmail);
    
    if (is_array($result) && isset($result['status']) && $result['status'] === true) {
        echo json_encode($result);
    } else {
        echo json_encode($result);
    }
}

function handleGetStats($transactions, $userEmail) {
    $result = $transactions->getTransactionStats($userEmail);
    
    if (is_array($result) && isset($result['status']) && $result['status'] === true) {
        echo json_encode($result);
    } else {
        echo json_encode($result);
    }
}

function validateTransactionType($type) {
    $validTypes = ['whatsapp', 'sms', 'both'];
    return in_array(strtolower($type), $validTypes) ? strtolower($type) : 'both';
}

function validateDate($date) {
    if (empty($date)) {
        return '';
    }
    
    // Try to parse the date
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    
    // Return in MySQL datetime format
    return date('Y-m-d H:i:s', $timestamp);
}

function validateLimit($limit) {
    $limit = (int)$limit;
    
    // Set reasonable bounds
    if ($limit < 1) {
        return 50;
    } elseif ($limit > 1000) {
        return 1000;
    }
    
    return $limit;
}

function validateOffset($offset) {
    $offset = (int)$offset;
    return $offset < 0 ? 0 : $offset;
}