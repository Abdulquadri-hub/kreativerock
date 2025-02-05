<?php

require_once 'SmsIntegration.php';
require_once 'ApiKeyManager.php';

class ExternalSmsApi {
    private $smsIntegration;
    private $db;
    private $apiKeyManager;
    private $apiLogTable = 'api_logs';

    public function __construct() {
        $this->smsIntegration = new SmsIntegration();
        $this->db = new dbFunctions();
        $this->apiKeyManager = new ApiKeyManager();
    }

    public function handleMessageRequest() {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        if (!isset($_SERVER["CONTENT_TYPE"]) || stripos($_SERVER["CONTENT_TYPE"], "application/json") === false) {
            return $this->jsonResponse(['error' => 'Unsupported Media Type'], 415);
        }

        $bearerToken = $this->getBearerToken();
        if (!$bearerToken) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        $userId = $this->apiKeyManager->validateBearer($bearerToken);
        if (!$userId) {
            return $this->jsonResponse(['error' => 'Invalid or expired token'], 401);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->jsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $requiredFields = ['destination', 'message'];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field])) {
                return $this->jsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        if ($this->isRateLimitExceeded($userId)) {
            return $this->jsonResponse(['error' => 'Rate limit exceeded'], 429);
        }

        $destinations = is_array($body['destination']) ? $body['destination'] : [$body['destination']];
        $validDestinations = $this->validatePhoneNumbers($destinations);
        if (empty($validDestinations)) {
            return $this->jsonResponse(['error' => 'No valid phone numbers provided'], 400);
        }

        if (!$this->validateMessage($body['message'])) {
            return $this->jsonResponse(['error' => 'Invalid message content'], 400);
        }

        $results = $this->smsIntegration->sendExternalBulkOneWaySms($validDestinations, $body['message']);

        $this->logApiCall($userId, $validDestinations, $body['message'], $results);

        return $this->jsonResponse([
            'status' => 'success',
            'data' => $results
        ], 200);
    }

    public function generateAPIKeys() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['status' => false, 'error' => 'Method not allowed'], 405);
        }
        
        $keys = $this->apiKeyManager->generateKeys();
        if ($keys) {
            return $this->jsonResponse([
                'status' => true,
                'message' => 'API keys generated successfully',
                'data' => $keys
            ], 201);
        } else {
            return $this->jsonResponse([
                'status' => false,
                'message' => 'Failed to generate API keys',
                'data' => null
            ], 500);
        }
    }

    public function generateBearerToken() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        if (!isset($_SERVER["CONTENT_TYPE"]) || stripos($_SERVER["CONTENT_TYPE"], "application/json") === false) {
            return $this->jsonResponse(['error' => 'Unsupported Media Type'], 415);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->jsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $requiredFields = ['public_key', 'secret_key'];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field])) {
                return $this->jsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $apiUserId = $this->apiKeyManager->validateKeys($body['public_key'], $body['secret_key']);
        if (!$apiUserId) {
            return $this->jsonResponse(['error' => 'Invalid API keys'], 401);
        }

        $token = $this->apiKeyManager->generateBearer($apiUserId);
        return $this->jsonResponse($token, 200);
    }
    
    private function isRateLimitExceeded($userId) {
        $data = $this->db->select($this->apiLogTable, "api_user_id = '$userId' and  timestamp = DATE_SUB(NOW(), INTERVAL 1 HOUR");
        return is_array($data) ?  count($data) >= 100 : null;
    }
    
    private function validatePhoneNumbers($numbers) {
        return array_filter($numbers, function($number) {
            return preg_match('/^\+?[1-9]\d{1,14}$/', $number);
        });
    }

    private function validateMessage($message) {
        $maxLength = 160; // Standard SMS length
        return strlen($message) > 0 && strlen($message) <= $maxLength;
    }
    
    private function logApiCall($apiUserId,$destinations, $message, $results){
        $log = $this->db->insert($this->apiLogTable, [
            'api_user_id' => $apiUserId,
            'destinations' => json_encode($destinations),
            'message' => $message,
            'results' => json_encode($results)
        ]);
        return $log;
    }

    private function getBearerToken() {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            return null;
        }
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function jsonResponse($data, $statusCode = 200){
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    
    
    
    
}