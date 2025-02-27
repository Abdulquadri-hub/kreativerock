<?php

require_once 'ResponseHandler.php';

class DotgoApi  {
    
    public $model;
    public $serverRoot;
    
    public function __construct(){
        $this->model = new Model();
        $this->serverRoot = "https://api.dotgo.com/rcs";
    }
    
    
    public function getSmsApiInfo($condition){
        return $this->model->findOne("sms_api_info", $condition);
    }
    
    public function updateSmsApiInfoDetails($query, $provider){
        return $this->model->update('sms_api_info', $query, "WHERE provider = $provider");
    } 
    
    public function runByQuerySelector($query){
        $res = $this->model->executeQuery($query);
        return $res;
    }
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
    public function sendIsTyping($userContact, $isTyping = 'active') 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId//messages";
        
        $data = [
            'RCSMessage' => [
                'isTyping' => $isTyping
            ],
            'messageContact' => [
                'userContact' => $userContact
            ]
        ];

        return $this->sendRequest('POST', $endpoint, $data);
    }
    
    public function getMessageStatus($msgId) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/messages/$msgId/status";
        return $this->sendRequest('GET', $endpoint);
    }
    
    public function revokeMessage($msgId) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/messages/$msgId/status";
        $data = [
            'RCSMessage' => [
                'status' => 'cancelled'
            ]
        ];

        return $this->sendRequest('PUT', $endpoint, $data);
    }
    
    public function checkRCSCapability($userContact) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/contactCapabilities?userContact=$userContact";
        return $this->sendRequest('GET', $endpoint);
    }
    
    public function sendReadNotification($msgId) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/messages/$msgId/status";
        $data = [
            'RCSMessage' => [
                'status' => 'displayed'
            ]
        ];

        return $this->sendRequest('PUT', $endpoint, $data);
    }
    
    public function uploadFile($fileType, $until, $fileContent = null, $fileUrl = null) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/files";
        
        $postFields = [
            'fileType' => $fileType,
            'until' => $until
        ];

        if ($fileContent !== null) 
        {
            $postFields['fileContent'] = new CURLFile($fileContent);
        } elseif ($fileUrl !== null) 
        {
            $postFields['fileUrl'] = $fileUrl;
        } else {
            throw new Exception("Either fileContent or fileUrl must be provided");
        }

        $ch = curl_init($this->serverRoot . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->getAccessToken()
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

    public function deleteFile($fileId) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/files/$fileId";
        return $this->sendRequest('DELETE', $endpoint);
    }

    public function getFileStatus($fileId) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/files/$fileId";
        return $this->sendRequest('GET', $endpoint);
    }
    
    public function sendMessage($userContact, $message, $messageType = 'textMessage', $enableFallback = false, $sendGipLink = false) 
    {
        $botId = $this->botId();
        $endpoint = "/bot/v1/$botId/messages/async";
        
        $queryParams = http_build_query([
            'enableFallback' => $enableFallback ? 'true' : 'false',
            'sendGipLink' => $sendGipLink ? 'true' : 'false'
        ]);
        $endpoint .= "?$queryParams";

        $data = [
            'RCSMessage' => [
                $messageType => $message
            ],
            'messageContact' => [
                'userContact' => $userContact
            ]
        ];

        return $this->sendRequest('POST', $endpoint, $data);
    }
    
    public function sendOneSmsMessage($phoneNumber, $message)
    {
        return $this->sendMessage($phoneNumber, $message, 'textMessage', true, false);
    }
    
    public function sendBulkSmsMessages($phoneNumbers, $message)
    {
        $results = [];
        foreach($phoneNumbers as $phoneNumber)
        {
            $results[$phoneNumber] = $this->sendOneSmsMessage($phoneNumber, $message);
        }
        return $results;
    }
    
    public function webhook($data)
    {
        if (!isset($data['event'])) {
            error_log("Invalid webhook payload: 'event' field is missing");
            exit;
        }

        switch ($data['event']) 
        {
            case 'message':
                return $this->handleIncomingMessage($data);
                break;
            case 'isTyping':
                return $this->handleMessageIsTyping($data);
                break;
            case 'messageStatus':
                return $this->handleMessageStatus($data);
                break;
            case 'response':
                return $this->handleSuggestedResponse($data);
                break;
            default:
                exit(json_encode(['status'=>false, 'message' => "Unknown event type: {$data['event']}"]));
                error_log("Unknown event type: {$data['event']}");
                
        }
    }
    
    public function recieveMessage($webHookData)
    {
        return $this->webhook($webHookData);
    }
    
    public function replyToMessage($phoneNumber, $message, $originalMessageId) 
    {
        $result = $this->sendOneSmsMessage($phoneNumber, $message);
        $this->sendReadNotification($originalMessageId);
        return $result;
    }

    public function checkDeliveryStatus($messageId) 
    {
        return $this->getMessageStatus($messageId);
    }
    
    private function getAccessToken() 
    {
        if($this->iSTokenValid()) 
        {
            return $this->accessToken();
        }

        $apiInfo = $this->getSmsApiInfo("provider='dotgo'");
        if(!empty($apiInfo) && count($apiInfo) > 0)
        {
            $clientId = $apiInfo['client_id'];
            $clientSecret = $apiInfo['client_secret'];
            $client_credentials = $apiInfo['grant_type'];
            
            $url = 'https://auth.dotgo.com/auth/oauth/token?grant_type=client_credentials';
            $headers = [
                'Authorization: Basic ' . base64_encode($clientId. ':' . $clientSecret),
                'Content-Type: application/x-www-form-urlencoded'
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            if ($httpCode === 200) 
            {
                $response = json_decode($result, true);
                if (isset($response['access_token'])) 
                {
                    $accessToken = $response['access_token'];
                    $token_type = $response['token_type'];
                    $expires_in = $response['expires_in'];
                    $scope = $response['scope'];
                    $accessTokenIssuedAt = date("Y-m-d H:i:s");
                
                    $query = "UPDATE sms_api_info 
                              SET access_token = '$accessToken', 
                              access_token_issued_at = '$accessTokenIssuedAt', 
                              access_token_validity = '$expires_in',  
                              token_type = '$token_type',
                              scope = '$scope'
                              WHERE provider = 'dotgo' ";
                
                    if($this->runByQuerySelector($query)) 
                    {
                        return $accessToken;
                    } else {
                        return json_encode(["status" => false, "code" => $httpCode, "message" => "Access token and other info failed to update or generate.", "token" => $response]);  
                   }
                   
                } else {
                
                    return json_encode(["status" => false, "code" => $httpCode, "message" => "Failed to retrieve access token.", "error" => $err]);
                }
            
            } else {
                return json_encode(["status" => false, "code" => $httpCode, "message" => $err]);
            }
        }else {
            return json_encode(["status" => false, "code" =>404, "message" => "Api info not found."]);
        }
    }
    
    public function iSTokenValid()
    {
        $apiInfo = $this->getSmsApiInfo("provider='dotgo'");
        if(!empty($apiInfo) && count($apiInfo) > 0)
        {
            if(!is_null($apiInfo['access_token']))
            {
                $tokenParts = explode('.', $this->accessToken());
                $tokenPayload = json_decode(base64_decode($tokenParts[1]), true);
                $expiresTime = $tokenPayload['exp'];
                
                if(isset($tokenPayload['exp']) && time() < $expiresTime)
                {
                    return true;
                }else {
                    return false;
                }
            }
            
        }else {
            
            return [];
        }
    }
    
    private function botId()
    {
        $apiInfo = $this->getSmsApiInfo("provider='dotgo'");
        if(!empty($apiInfo) && count($apiInfo) > 0)
        {
            if(!empty($apiInfo['client_id']))
            {
                return $apiInfo['client_id'];
            }
        }else {
            
            return [];
        }
    }
    
    private function accessToken()
    {
        
        $apiInfo = $this->getSmsApiInfo("provider='dotgo'");
        if(!empty($apiInfo) && count($apiInfo) > 0)
        {
            if(!empty($apiInfo['access_token']))
            {
                return $apiInfo['access_token'];
            }
        }else {
            
            return [];
        }
    }
    
    private function sendRequest($method, $endpoint, $data = null, $extraHeaders = []) 
    {
        
        $url = $this->serverRoot . $endpoint;
        $headers = array_merge([
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json'
        ], $extraHeaders);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new ResponseHandler(json_encode([
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ]));
    }
    
    public function handleMessageStatus($data)
    {
        $messageData = [
            'type' => 'messageStatus',
            'messageId' => $data['RCSMessage']['msgId'],
            'status' => $data['RCSMessage']['status'],
            'timestamp' => $data['RCSMessage']['timestamp'],
            'userContact' => $data['messageContact']['userContact']
        ];
        return $messageData;
    }
    
    public function handleMessageIsTyping($data)
    {
        $messageData =  [
            'type' => 'isTyping',
            'userContact' => $data['messageContact']['userContact'],
            'isTyping' => $data['RCSMessage']['isTyping'],
            'timestamp' => $data['RCSMessage']['timestamp']
        ];
        
        file_put_contents('webhook_log.txt', "User is typing: user:". $data. "\n", FILE_APPEND);
        return $messageData;
    }
    
    public function handleIncomingMessage($data)
    {
        $message = $data['RCSMessage'];
        $userContact = $data['messageContact']['userContact'];

        $messageType = null;
        $messageContent = null;
        if (isset($message['textMessage'])) {
            $messageType = 'text';
            $messageContent = $message['textMessage'];
        } elseif (isset($message['fileMessage'])) {
            $messageType = 'file';
            $messageContent = $message['fileMessage'];
        } elseif (isset($message['audioMessage'])) {
            $messageType = 'audio';
            $messageContent = $message['audioMessage'];
        } elseif (isset($message['geolocationPushMessage'])) {
            $messageType = 'location';
            $messageContent = $message['geolocationPushMessage'];
        }

        $messageData =  [
            'type' => 'incomingMessage',
            'messageId' => $message['msgId'],
            'timestamp' => $message['timestamp'],
            'userContact' => $userContact,
            'messageType' => $messageType,
            'messageContent' => $messageContent
        ];
        
        file_put_contents('webhook_log.txt', "User suggested response: data: $data \n", FILE_APPEND);
        return $messageData;
    }
    
    public function handleSuggestedResponse($data) {
        $messageData =  [
            'type' => 'suggestedResponse',
            'messageId' => $data['RCSMessage']['msgId'],
            'timestamp' => $data['RCSMessage']['timestamp'],
            'userContact' => $data['messageContact']['userContact'],
            'response' => $data['RCSMessage']['suggestedResponse']['response']
        ];
        file_put_contents('webhook_log.txt', "User suggested response: data: $data \n", FILE_APPEND);
        return $messageData;
    }
    
}