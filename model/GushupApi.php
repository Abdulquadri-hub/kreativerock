<?php

class GupshupAPI {
    private $baseUrl = 'https://partner.gupshup.io/partner';
    private $email;
    private $password;
    private $token = null;
    private $appTokens = [];
    private $currentAppId = "1e1b0a92-6a16-4e29-b738-da3a245f24df";
    private $db;

    public function __construct($email = "info@kreativerock.com", $password = "123456789Ab####", bool $autoLogin = true) {
        $this->email = $email;
        $this->password = $password;
        $this->db = new dbFunctions();
        
        if ($autoLogin) {
            $this->login();
        }
    }

    public function generateToken(): array {
        // Check if a valid token exists in the database
        $tokenData = $this->getTokenFromDb();
        if ($tokenData && strtotime($tokenData['expires_at']) > time()) {
            $this->token = $tokenData['token'];
            return ['token' => $this->token];
        }

        // If no valid token exists, generate a new one
        $endpoint = '/account/login';
        $url = $this->baseUrl . $endpoint;

        // Prepare POST data
        $postData = http_build_query([
            'email' => $this->email,
            'password' => $this->password
        ]);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        // Decode JSON response
        $responseData = json_decode($response, true);

        // Check for successful response
        if ($httpCode !== 200 || !isset($responseData['token'])) {
            throw new Exception('Failed to generate token. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        // Store token for future use
        $this->token = $responseData['token'];

        $this->saveTokenToDb($this->token);

        return $responseData;
    }

    public function login(): void {
        if (!$this->token) {
            $this->generateToken();
        }
    }

    public function getAppToken(?string $appId = null): array {
        if (!$this->token) {
            throw new Exception('Partner token not available. Please generate token first.');
        }

        $appId = $appId ?? $this->currentAppId;

        $endpoint = "/app/{$appId}/token/";
        $url = $this->baseUrl . $endpoint;

        $token = trim($this->token);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: ' . $token 
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200 || !isset($responseData['status']) || $responseData['status'] !== 'success') {
            throw new Exception('Failed to get app token. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        $this->appTokens[$appId] = $responseData['token']['token'];

        return $responseData;
    }

    public function getCurrentAppId(): string {
        return $this->currentAppId;
    }

    public function setCurrentAppId(string $appId): void {
        $this->currentAppId = $appId;
    }

    public function createTemplate(string $appId, array $templateData): array {

        if (!isset($this->appTokens[$appId])) {
            $this->getAppToken($appId);
        }

        $endpoint = "/app/{$appId}/templates";
        $url = $this->baseUrl . $endpoint;

        $requiredFields = [
            'elementName',
            'languageCode',
            'content',
            'vertical',
            'category'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($templateData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        $templateData['templateType'] = $templateData['templateType'] ?? 'TEXT';
        $templateData['enableSample'] = $templateData['enableSample'] ?? 'true';
        $templateData['allowTemplateCategoryChange'] = $templateData['allowTemplateCategoryChange'] ?? 'false';

        if (isset($templateData['buttons']) && is_array($templateData['buttons'])) {
            $templateData['buttons'] = json_encode($templateData['buttons']);
        }
        else{
            unset($templateData['buttons']);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($templateData),
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/x-www-form-urlencoded',
                'token: ' . $this->appTokens[$appId]
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200 || !isset($responseData['status']) || $responseData['status'] !== 'success') {
            throw new Exception('Failed to create template. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    public function getToken(): ?string {
        return $this->token;
    }

    public function getTemplates(string $appId): array {
        // Ensure we have an app token
        if (!isset($this->appTokens[$appId])) {
            $this->getAppToken($appId);
        }

        $endpoint = "/app/{$appId}/templates";
        $url = $this->baseUrl . $endpoint;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: ' . $this->appTokens[$appId]
            ]
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Failed to get templates. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    public function uploadTemplateMedia(string $appId, string $filePath, string $fileType): array {
        if (!file_exists($filePath)) {
            exit(badRequest(400, "File not found: {$filePath}"));
        }

        // Ensure we have an app token
        if (!isset($this->appTokens[$appId])) {
            $this->getAppToken($appId);
        }

        $endpoint = "/app/{$appId}/upload/media";
        $url = $this->baseUrl . $endpoint;

        // Create CURLFile object
        $cfile = new CURLFile($filePath);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file_type' => $fileType,
                'file' => $cfile
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->appTokens[$appId],
                'Accept: application/json'
            ]
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            exit(badRequest(400, 'Curl error: ' . curl_error($ch)));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            exit(error($responseData, 442, 'Failed to upload media. HTTP Code: ' . $httpCode . '. Response: ' . $response));
        }

        return $responseData;
    }

    public function editTemplate(string $appId, string $templateId, array $templateData): array {
        // Ensure we have an app token
        if (!isset($this->appTokens[$appId])) {
            $this->getAppToken($appId);
        }

        $requiredFields = [
            'elementName',
            'languageCode',
            'content',
            'category'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($templateData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        $endpoint = "/app/{$appId}/templates/{$templateId}";
        $url = $this->baseUrl . $endpoint;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => http_build_query($templateData),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->appTokens[$appId],
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ]
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Failed to edit template. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    public function deleteTemplate(string $appId, string $elementName): array {
        // Ensure we have an app token
        if (!isset($this->appTokens[$appId])) {
            $this->getAppToken($appId);
        }
    
        $endpoint = "/app/{$appId}/template/{$elementName}";
        $url = $this->baseUrl . $endpoint;
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                'accept' => 'application/json',
                'content-type' => 'application/x-www-form-urlencoded',
                'token: ' . $this->appTokens[$appId],
            ]
        ]);
    
        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
    
        curl_close($ch);
    
        $responseData = json_decode($response, true);
    
        if ($httpCode !== 200) {
            throw new Exception('Failed to delete template. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }
    
        return $responseData;
    }

    public function sendMessage(string $appId, string $templateId, array $messageData): array {
        
        if (!isset($this->appTokens[$appId])) {
            $this->getAppToken($appId);
        }
    
        $requiredFields = [
            'source',
            'destination',
            'template',
            'src.name'
        ];
    
        foreach ($requiredFields as $field) {
            if (!isset($messageData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
    
        $endpoint = "/app/{$appId}/template/msg";
        $url = $this->baseUrl . $endpoint;
    
        $templateData = [
            'id' => $templateId,
            'params' => []
        ];
    
        $messageContent = $this->buildMessageContent($messageData);
    
        $postData = array_merge($messageData, [
            'template' => json_encode($templateData),
            'message' => json_encode($messageContent)
        ]);
    
        $ch = curl_init();
    
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->appTokens[$appId],
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
    
        curl_close($ch);
    
        $responseData = json_decode($response, true);
    
        if ($httpCode !== 200) {
            throw new Exception('Failed to send message. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }
    
        return $responseData;
    }

    public function formatPhoneNumber(string $phoneNumber, string $countryCode = '91'): string {
        // Remove any non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If the number already starts with the country code, return it
        if (strpos($cleaned, $countryCode) === 0) {
            return $cleaned;
        }
        
        // If the number starts with a zero, remove it
        if (strpos($cleaned, '0') === 0) {
            $cleaned = substr($cleaned, 1);
        }
        
        // Add country code
        return $countryCode . $cleaned;
    }

    public function buildMessageContent(array $messageData): array {
        if (!isset($messageData['message_type'])) {
            throw new Exception("Message type is required");
        }
    
        switch (strtolower($messageData['message_type'])) {
            case 'text':
                return [
                    'type' => 'text',
                    'text' => $messageData['message'] ?? ''
                ];
    
            case 'video':
                if (!isset($messageData['link']) || !isset($messageData['id'])) {
                    throw new Exception("Video link and id are required for video messages");
                }
                return [
                    'type' => 'video',
                    'video' => [
                        'link' => $messageData['media_url'],
                        'id' => $messageData['media_id']
                    ]
                ];
    
            case 'image':
                if (!isset($messageData['link']) || !isset($messageData['id'])) {
                    throw new Exception("Image link and id are required for image messages");
                }
                return [
                    'type' => 'image',
                    'image' => [
                        'link' => $messageData['media_url'],
                        'id' => $messageData['media_id']
                    ]
                ];
    
            case 'document':
                if (!isset($messageData['link']) || !isset($messageData['id'])) {
                    throw new Exception("Document link and id are required for document messages");
                }
                return [
                    'type' => 'document',
                    'document' => [
                        'link' => $messageData['media_url'],
                        'id' => $messageData['media_id']
                    ]
                ];
    
            case 'location':
                if (!isset($messageData['location'])) {
                    throw new Exception("Location data is required for location messages");
                }
                return [
                    'type' => 'LOCATION',
                    'LOCATION' => $messageData['location_data']
                ];
    
            default:
                throw new Exception("Unsupported message type: {$messageData['type']}");
        }
    }

    // Onbaording Partner Apis

    public function createApp(array $appData): array {
        if (!$this->token) {
            throw new Exception('Partner token not available. Please generate token first.');
        }

        $endpoint = '/app';
        $url = $this->baseUrl . $endpoint;

        // Validate required fields
        $requiredFields = ['name'];
        foreach ($requiredFields as $field) {
            if (!isset($appData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Set optional fields with defaults
        $appData['templateMessaging'] = $appData['templateMessaging'] ?? false;
        $appData['disableOptinPrefUrl'] = $appData['disableOptinPrefUrl'] ?? false;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($appData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'token: ' . $this->token
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Failed to create app. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    public function updateApp(string $appId, array $appData): array {
        if (!$this->token) {
            throw new Exception('Partner token not available. Please generate token first.');
        }

        $endpoint = "/app/{$appId}";
        $url = $this->baseUrl . $endpoint;

        // Validate storageRegion if provided
        $validRegions = ['BR', 'DE', 'CH', 'GB', 'BH', 'ZA', 'AE', 'US', 'CA', 'AU', 'ID', 'IN', 'JP', 'SG', 'KR'];
        if (isset($appData['storageRegion']) && !in_array($appData['storageRegion'], $validRegions)) {
            throw new Exception('Invalid storage region provided');
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => http_build_query($appData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'token: ' . $this->token
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Failed to update app. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    public function getAppDetails(string $appId): array {
        if (!$this->token) {
            throw new Exception('Partner token not available. Please generate token first.');
        }

        $endpoint = "/app/{$appId}/details";
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                'token: ' . $this->token
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Failed to get app details. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    public function listApps(?array $filters = []): array {
        if (!$this->token) {
            throw new Exception('Partner token not available. Please generate token first.');
        }

        $endpoint = '/app/list';
        $url = $this->baseUrl . $endpoint;

        // Add optional query parameters
        $queryParams = [];
        if (isset($filters['name'])) {
            $queryParams['name'] = $filters['name'];
        }
        if (isset($filters['phone'])) {
            $queryParams['phone'] = $filters['phone'];
        }
        if (isset($filters['pageNo'])) {
            $queryParams['pageNo'] = $filters['pageNo'];
        }
        if (isset($filters['pageSize'])) {
            $queryParams['pageSize'] = $filters['pageSize'];
        }

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                'token: ' . $this->token
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Failed to list apps. HTTP Code: ' . $httpCode . '. Response: ' . $response);
        }

        return $responseData;
    }

    private function getTokenFromDb(): ?array {
        $where = "email = '" . $this->db->escape($this->email) . "'";
        return $this->db->find('gupshup_tokens', $where, 'created_at DESC', 1);
    }

    private function saveTokenToDb(string $token): void {
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $data = [
            'email' => $this->email,
            'token' => $token,
            'expires_at' => $expiresAt
        ];

        $this->db->insert('gupshup_tokens', $data);
    }

}