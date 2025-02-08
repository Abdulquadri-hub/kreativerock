<?php

class ExternalSmsApi {
    private $smsIntegration;
    private $db;
    private $apiKeyManager;
    private $apiLogTable = 'api_logs';
    private const MAX_BATCH_SIZE = 100;
    private const RATE_LIMIT_PER_HOUR = 1000;
    private const MESSAGE_MAX_LENGTH = 160;
    private $usersTable = "users";

    public function __construct() {
        $this->smsIntegration = new SmsIntegration();
        $this->db = new dbFunctions();
        $this->apiKeyManager = new ApiKeyManager();
    }

    public function handleMessageRequest() {
        try {
            $this->validateRequest();
            
            $apiKey = $this->getApiKey();
            
            $userId = $this->apiKeyManager->validateApiKey($apiKey);
           
            if (!$userId) {
                return $this->jsonResponse([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Authentication failed',
                ], 401);
            }

            $body = $this->getValidatedRequestBody();

            if ($this->isRateLimitExceeded($userId)) {
                return $this->jsonResponse([
                    'status' => false,
                    'code' => 429,
                    'message' => 'Rate limit exceeded',
                    'limit' => self::RATE_LIMIT_PER_HOUR,
                    'reset_in' => $this->getTimeUntilRateLimit($userId)
                ], 429);
            }


            $recipients = $this->validateAndNormalizeRecipients($body['recipient']);
            $message = $this->sanitizeMessage($body['message']);

            $requiredUnits = count($recipients);

            $user = $this->db->find($this->usersTable, "id = '{$userId}'");

            if (!$this->smsIntegration->deductUnits($user['email'], $requiredUnits)) {
                return $this->jsonResponse([
                    'status' => false,
                    'code' => 442,
                    'message' => "Insufficient SMS units",
                ], 442);
            }

            $idempotencyKey = $body['idempotency_key'] ?? $this->generateIdempotencyKey();

            if ($this->isDuplicateRequest($userId, $idempotencyKey)) {
                return $this->getCachedResponse($userId, $idempotencyKey);
            }

            $results = $this->smsIntegration->sendExternalBulkOneWaySms($recipients, $message);
            
            $responses = [];
            foreach ($results as $phoneNumber => $result) {
                
                $responses[] = [
                    'recipient' => $phoneNumber,
                    'status' => $result->isSuccess() ? $result->getMessageStatus() : 'failed',
                    'message_id' => $result->getMessageId(),
                    'error' => $result->isSuccess() ? null : $result->getMessage()
                ];

                $this->logApiCall($userId, $recipients, $message, $responses, $result->getMessageId(), $idempotencyKey);
            }
            
            $response = [
                'status' => true,
                'code' => 200,
                'recipients_count' => count($recipients),
                'data' => $responses
            ];
            return $this->jsonResponse($response, 200);
            
        } catch (ValidationException $e) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 405,
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            error_log("API Error: " . $e->getMessage());
            return $this->jsonResponse([
                'status' => false,
                'code' => 500,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    private function validateRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse([
                'status' => false,
                'code' => 405,
                'message' => "Method not allowed",
            ], 405);
        }

        if (!isset($_SERVER["CONTENT_TYPE"]) || stripos($_SERVER["CONTENT_TYPE"], "application/json") === false) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 415,
                'message' => "Unsupported Media Type",
            ], 415);
        }
    }

    private function getValidatedRequestBody() {
        $body = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 400,
                'message' => "Invalid JSON payload",
            ], 400);
        }

        $requiredFields = ['recipient', 'message'];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field])) {
                return $this->jsonResponse([
                    'status' => false,
                    'code' => 400,
                    'message' => "Missing required field: $field",
                ], 400);
            }
        }

        return $body;
    }

    private function validateAndNormalizeRecipients($recipients) {
        $recipients = is_array($recipients) ? $recipients : [$recipients];
        
        if (count($recipients) > self::MAX_BATCH_SIZE) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 413,
                'message' => "Maximum batch size exceeded: " . self::MAX_BATCH_SIZE
            ], 413);
        }

        $validRecipients = array_filter($recipients, function($number) {
            return preg_match('/^\+?[1-9]\d{10,14}$/', $number);
        });

        if (empty($validRecipients)) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 400,
                'message' => "No valid phone numbers provided"
            ], 400);
        }

        return array_values($validRecipients); // Re-index array
    }

    private function sanitizeMessage($message) {
        if (strlen($message) > self::MESSAGE_MAX_LENGTH) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 400,
                'message' => "Message exceeds maximum length of " . self::MESSAGE_MAX_LENGTH
            ], 400);
        }

        // Basic XSS/injection prevention
        $message = strip_tags(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        return trim($message);
    }

    private function generateMessageId() {
        return 'msg_' . uniqid() . '_' . time();
    }

    private function generateIdempotencyKey() {
        return 'idkey_' . bin2hex(random_bytes(16));
    }

    private function isDuplicateRequest($userId, $idempotencyKey) {
        // Implementation for checking duplicate requests
        $existingRequest = $this->db->find($this->apiLogTable, "user_id = '{$userId}' AND idempotency_key = '{$idempotencyKey}' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        return !empty($existingRequest);
    }

    private function getCachedResponse($userId, $idempotencyKey) {

        $cachedResponse = $this->db->find($this->apiLogTable, "*","user_id = '{$userId}' AND idempotency_key = '{$idempotencyKey}'");

        return $this->jsonResponse(json_decode($cachedResponse['response'], true), 200);
    }

    private function getApiKey() {
        $headers = apache_request_headers();
        if (!isset($headers['X-API-Key'])) {
            return $this->jsonResponse([
                'status' => false,
                'code' => 400,
                'message' => 'API key is required'
            ], 400);
        }
        return $headers['X-API-Key'];
    }

    private function isRateLimitExceeded($userId) {
        $data = $this->db->select($this->apiLogTable, "*", "user_id = '{$userId}' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        if(is_array($data)){
            $count = count($data);
            return $count >= self::RATE_LIMIT_PER_HOUR;
        }
    }

    private function getTimeUntilRateLimit($userId) {
        $oldestRequest = $this->db->select($this->apiLogTable,
            "user_id = '{$userId}' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY created_at ASC LIMIT 1"
        );
        return strtotime($oldestRequest['created_at']) + 3600 - time();
    }

    private function logApiCall($userId, $recipients, $message, $results, $messageId, $idempotencyKey) {
        $this->db->insert($this->apiLogTable, [
            'user_id' => $userId,
            'message_id' => $messageId,
            'idempotency_key' => $idempotencyKey,
            'recipients' => json_encode($recipients),
            'message' => $message,
            'response' => json_encode($results),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}

class ValidationException extends Exception {}