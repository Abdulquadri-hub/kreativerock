<?php


class ResponseHandler {
    
    private $response;
    private $decodedResponse;
    
    public function __construct($jsonResponse)
    {
        $this->response = $jsonResponse;
        $this->decodedResponse = json_decode($jsonResponse, true);
    }
    
    public function isSuccess()
    {
        $successCodes = [200, 201, 202, 204];
        return in_array($this->getStatusCode(), $successCodes);
    }
    
    public function getDecodedResponse() {
        return $this->decodedResponse;
    }
    
    
    public function getStatusCode()
    {
        return isset($this->decodedResponse['code']) ? $this->decodedResponse['code'] : null;
    }
    
    public function getData()
    {
        return isset($this->decodedResponse['data']) ? $this->decodedResponse['data'] : null;
    }
    
    public function getMessage() 
    {
        if (isset($this->decodedResponse['data']['reason']['text'])) {
            return $this->decodedResponse['data']['reason']['text'];
        }
        return null;
    }
    
    public function getMessageId()
    {
        if (isset($this->decodedResponse['data']['RCSMessage']['msgId'])) 
        {
            return $this->decodedResponse['data']['RCSMessage']['msgId'];
        }
        return null;
    }
    
    public function getMessageStatus()
    {
        if (isset($this->decodedResponse['data']['RCSMessage']['status'])) 
        {
            return $this->decodedResponse['data']['RCSMessage']['status'];
        }
        return null;
    }
    
    public function getErrorDetails()
    {
        if(!$this->isSuccess())
        {
            return [
                'code' => $this->getStatusCode(),
                'message' => $this->getMessage()
            ];
        }
        
        return null;
    }

}






