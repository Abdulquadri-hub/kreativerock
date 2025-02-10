<?php

class WhatsAppIntegration {
    // API configuration
    private $userId;
    private $password;
    private $baseUrl;
    private $defaultConfig;
    private $encryptor;
    
    // Response tracking
    private $lastResponse = null;
    private $lastError = null;

    /**
     * Initialize the WhatsApp API client with credentials
     * 
     * @param string $userId Your API user ID
     * @param string $password Your API password
     * @param string|null $encryptionKey Optional Gupshup encryption key
     */
    public function __construct(
        string $userId = "user_id", 
        string $password = "user_password",
        ?string $encryptionKey = null
    ) {
        $this->userId = $userId;
        $this->password = $password;
        $this->baseUrl = 'https://media.smsgupshup.com/GatewayAPI/rest';
        
        // Set up default configuration that will be used in all requests
        $this->defaultConfig = [
            'userid' => $this->userId,
            'password' => $this->password,
            'v' => '1.1',
            'auth_scheme' => 'plain',
            'format' => 'json'
        ];

        // Initialize encryptor if key is provided
        if ($encryptionKey) {
            $this->encryptor = new GupshupEncryption($encryptionKey);
        }
    }

    /**
     * Send a text message to a WhatsApp number
     */
    public function sendText(
        string $phoneNumber,
        string $message,
        bool $isTemplate = false,
        ?string $header = null,
        ?string $footer = null
    ): bool {
        $params = array_merge($this->defaultConfig, [
            'method' => 'SendMessage',
            'msg_type' => 'TEXT',
            'msg' => $message,
            'send_to' => $phoneNumber
        ]);

        if ($isTemplate) {
            $params['isTemplate'] = 'true';
            if ($header) $params['header'] = $header;
            if ($footer) $params['footer'] = $footer;
        }

        return $this->sendRequest($params);
    }

    /**
     * Send a media message (image, video, or document)
     */
    public function sendMedia(
        string $phoneNumber,
        string $mediaType,
        string $mediaUrl,
        ?string $caption = null,
        bool $isTemplate = false
    ): bool {
        $params = array_merge($this->defaultConfig, [
            'method' => 'SendMediaMessage',
            'msg_type' => strtoupper($mediaType),
            'media_url' => $mediaUrl,
            'send_to' => $phoneNumber
        ]);

        if ($caption) $params['caption'] = $caption;
        if ($isTemplate) $params['isTemplate'] = 'true';

        return $this->sendRequest($params);
    }

    /**
     * Send an interactive message (with buttons or list)
     */
    public function sendInteractive(
        string $phoneNumber,
        string $message,
        string $interactiveType,
        array $action,
        ?string $header = null,
        ?string $footer = null
    ): bool {
        $params = array_merge($this->defaultConfig, [
            'method' => 'SendMessage',
            'msg' => $message,
            'interactive_type' => $interactiveType,
            'action' => json_encode($action),
            'send_to' => $phoneNumber
        ]);

        if ($header) $params['header'] = $header;
        if ($footer) $params['footer'] = $footer;

        return $this->sendRequest($params);
    }

    /**
     * Handle user consent (opt-in or opt-out)
     */
    public function handleConsent(string $phoneNumber, bool $optIn = true): bool {
        $params = array_merge($this->defaultConfig, [
            'method' => $optIn ? 'OPT_IN' : 'OPT_OUT',
            'phone_number' => $phoneNumber,
            'channel' => 'WHATSAPP'
        ]);

        return $this->sendRequest($params, 'GET');
    }

    /**
     * Send HTTP request to the WhatsApp API
     */
    private function sendRequest(array $params, string $method = 'POST'): bool {
        $ch = curl_init();
        
        if ($method === 'GET') {
            // For GET requests, build the query string
            $queryString = http_build_query($params);
            
            // If encryption is enabled, encrypt the query string
            if ($this->encryptor) {
                $url = $this->baseUrl . '?encrdata=' . urlencode($this->encryptor->encrypt($queryString));
            } else {
                $url = $this->baseUrl . '?' . $queryString;
            }
            
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            
            // For POST requests, handle encryption if enabled
            if ($this->encryptor) {
                $queryString = http_build_query($params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, 'encrdata=' . urlencode($this->encryptor->encrypt($queryString)));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $this->lastError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        if ($response !== false) {
            $this->lastResponse = json_decode($response, true);
            return $httpCode >= 200 && $httpCode < 300;
        }

        return false;
    }

    /**
     * Get the last API response
     */
    public function getLastResponse() {
        return $this->lastResponse;
    }

    /**
     * Get the last error message
     */
    public function getLastError() {
        return $this->lastError;
    }
}

class GupshupEncryption {
    private const GCM_IV_LENGTH = 12;
    private const GCM_TAG_LENGTH = 16;
    private $key;

    public function __construct(string $base64UrlKey) {
        $this->key = $this->base64UrlDecode($base64UrlKey);
    }

    public function encrypt(string $data): string {
        $iv = random_bytes(self::GCM_IV_LENGTH);
        
        $tag = '';
        $ciphertext = openssl_encrypt(
            $data,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            "",
            self::GCM_TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        $finalBuffer = $iv . $ciphertext . $tag;
        return $this->base64UrlEncode($finalBuffer);
    }

    private function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}