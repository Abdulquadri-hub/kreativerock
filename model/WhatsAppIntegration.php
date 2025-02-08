<?php

class WhatsAppIntegration {
    // API configuration
    private string $userId ;
    private string $password;
    private string $baseUrl;
    private array $defaultConfig;
    
    // Response tracking
    private $lastResponse = null;
    private $lastError = null;

    /**
     * Initialize the WhatsApp API client with credentials
     * 
     * @param string $userId Your API user ID
     * @param string $password Your API password
     */
    public function __construct(string $userId = "user_id", string $password = "user_password") {
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
    }

    /**
     * Send a text message to a WhatsApp number
     * 
     * @param string $phoneNumber Recipient's phone number
     * @param string $message Text message content
     * @param bool $isTemplate Whether the message is a template
     * @param string|null $header Optional header for template messages
     * @param string|null $footer Optional footer for template messages
     * @return bool Success status
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
     * 
     * @param string $phoneNumber Recipient's phone number
     * @param string $mediaType Type of media (IMAGE, VIDEO, DOCUMENT)
     * @param string $mediaUrl URL of the media file
     * @param string|null $caption Optional caption for the media
     * @param bool $isTemplate Whether the message is a template
     * @return bool Success status
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
     * 
     * @param string $phoneNumber Recipient's phone number
     * @param string $message Main message content
     * @param string $interactiveType Type of interactive message (BUTTON, LIST)
     * @param array $action Interactive elements configuration
     * @param string|null $header Optional header
     * @param string|null $footer Optional footer
     * @return bool Success status
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
     * 
     * @param string $phoneNumber User's phone number
     * @param bool $optIn True for opt-in, false for opt-out
     * @return bool Success status
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
     * 
     * @param array $params Request parameters
     * @param string $method HTTP method (POST or GET)
     * @return bool Success status
     */
    private function sendRequest(array $params, string $method = 'POST'): bool {
        $ch = curl_init();
        
        if ($method === 'GET') {
            $url = $this->baseUrl . '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
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
     * 
     * @return array|null Last response or null if no response
     */
    public function getLastResponse() {
        return $this->lastResponse;
    }

    /**
     * Get the last error message
     * 
     * @return string|null Last error message or null if no error
     */
    public function getLastError() {
        return $this->lastError;
    }
}