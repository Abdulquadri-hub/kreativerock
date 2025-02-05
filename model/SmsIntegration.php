<?php

require_once 'DotgoApi.php';

class SmsIntegration {
    private $api;
    private $db;
    private $messagesTable = 'messages';
    private $messageLogsTable = 'message_logs';
    private $rcsUsersTable = 'rcs_users';
    private $conversationsTable = 'conversations';
    private $keyWordsTable = 'keywords';
    private $promptsTable = 'prompts';
    private $smsTransactionsTable = 'sms_transactions';
    private $smsPackagesTable = 'sms_packages';
    private $userUnitsTable = 'user_units';
    private $usersTable = 'users';

    public function __construct() {
        $this->api = new DotgoApi();
        $this->db = new dbFunctions();
    }

    public function sendOneWaySms($phoneNumber, $message) 
    {
        $response = $this->api->sendOneSmsMessage($phoneNumber, $message);
        
        if ($response->isSuccess()) 
        {
            return $response;
        } else {
            return $response->getErrorDetails();
        }
    }

    public function sendBulkOneWaySms($phoneNumbers, $message) 
    {
        $results = $this->api->sendBulkSmsMessages($phoneNumbers, $message);
        $responses = [];
    
        foreach ($results as $phoneNumber => $response) 
        {
            if ($response->isSuccess()) 
            {
                $responses[$phoneNumber] = $response;
            } else {
                $responses[$phoneNumber] = $response->getErrorDetails();
            }
        }
        return $responses;
    }
    
    public function sendExternalBulkOneWaySms($phoneNumbers, $message) 
    {
        $results = $this->api->sendBulkSmsMessages($phoneNumbers, $message);
        $responses = [];
    
        foreach ($results as $phoneNumber => $response) 
        {
            if ($response->isSuccess()) 
            {
                $responses = $response->getData();
            } else {
                $responses = $response->getErrorDetails();
            }
        }
        return $responses;
    }
    
    public function logMessage($messageId, $event, $details) {
        try {
            $this->db->insert($this->messageLogsTable, [
                'message_id' => $messageId,
                'event' => $event,
                'details' => json_encode($details),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            $this->logSystem('error', 'Failed to log message', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function getDashboardStats($email) {
        $stats = [
            'total_unit_spent' => $this->getTotalSpentUnitsQty($email),
            'unit_balance' => $this->getTotalUnitsBalance($email),
            'total_registered_users' => $this->getTotalRegisteredUsers(),
            'total_online_users' => $this->getTotalOnlineUsers(),
            'total_sms_accounts' => $this->getTotalSmsAccounts(),
            'total_whatsapp_accounts' => $this->getTotalWhatsappAccounts()
        ];

        return $stats;
    }
    
    public function getTotalPurchasedUnitsQty($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $totalPurchasedUnits = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}'");
        if(!$totalPurchasedUnits){
            return 0;
        }
        else{
            return $totalPurchasedUnits['total_purchased_qty'];
        }
    }
    
    public function getTotalUnitsBalance($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $unit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}'");
        if(!$unit){
            return 0;
        }
        else{
            if($unit['total_used_qty'] == 0){
                return $this->getTotalPurchasedUnitsQty($email);
            }
            else{
                return $unit['total_unit_balance'];
            }
        }
    }
    
    public function getTotalSpentUnitsQty($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $totalSpentUnits = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}'");
        if(!$totalSpentUnits){
            return 0;
        }
        else{
            return $totalSpentUnits['total_used_qty'];
        } 
    }
    
    public function deductUnits($email, $unitsToDeduct) {
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return false;
        }
        
        $currentBalance = $this->getTotalPurchasedUnitsQty($email);
        $totalUsed = $this->getTotalSpentUnitsQty($email);

        if ($currentBalance < $unitsToDeduct) {
            return false; 
        }

        $newBalance = ($currentBalance - $unitsToDeduct);
        $totalUsed += $unitsToDeduct; 
        
        $userUnit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}'");
        if(empty($userUnit)){
            return false;
        }
         
        $this->db->update($this->userUnitsTable, ["total_used_qty" => $totalUsed], "user_id = '{$user['id']}'");
        $this->db->update($this->userUnitsTable, ["total_unit_balance" => $newBalance], "user_id = '{$user['id']}'");

        return true;
    }
    
    public function handleMessageStatus($webhookData){
        $messageData = $this->api->webhook($webhookData);

        if ($messageData['type'] == 'messageStatus') 
        {
            $phoneNumber = $messageData['userContact'];
            $status = $messageData['status'];
            $messageId = $messageData['messageId'];
            $timestamp = $messageData['timestamp'];
            
            $this->updateMessagesStatus($messageId, $status);
        }
    }

    public function checkMessageStatus($messageId) 
    {
        $response = $this->api->checkDeliveryStatus($messageId);
        
        if ($response->isSuccess()) 
        {
            return $response->getData();
        } else {
           return  $response->getErrorDetails();
        }
    }

    public function checkRcsCapability($phoneNumber) {
        $response = $this->api->checkRCSCapability($phoneNumber);
        
        if ($response->isSuccess()) {
            $capabilities = $response->getData();
            return $capabilities;
        } else {
            $error = $response->getErrorDetails();
            return $error;
        }
    }
    
    public function getOrCreateRcsUser($phoneNumber) 
    {
        $user = $this->db->select($this->rcsUsersTable, 'id', "phone_number = '$phoneNumber' AND status = 'open'", 'id', 1);
        if (!empty($user)) 
        {
            return $user[0]['id'];
            
        } else {
            $userId = $this->db->insert($this->rcsUsersTable, [
                'phone_number' => $phoneNumber
            ]);
            return $userId;
        }
    }

    public function getOrCreateConversation($userId) 
    {
        
        $conversation = $this->db->select($this->conversationsTable, 'conversation_id', "rcs_user_id  = $userId AND status = 'open'");
        if(!empty($conversation)) 
        {
            return $conversation[0]['conversation_id'];
            
        } else {
            return $this->db->insert($this->conversationsTable, [
                'rcs_user_id' => $userId
            ]);
        }
    }
    
    private function getTotalRegisteredUsers() {
        return $this->db->selectSum('users', "*");
    }

    private function getTotalOnlineUsers() {
        return $this->db->selectSum('users',  "*", "online = 'YES'");
    }

    private function getTotalSmsAccounts() {
        return $this->db->selectSum('rcs_users',  "*", "status = 'open'");
    }

    private function getTotalWhatsappAccounts() {
        return 0;
    }
    
    private function createMessage($conversationId, $userId, $type, $direction, $content, $rcsMessageId, $error = NULL) {
        $messageId = $this->db->insert($this->messagesTable, [
            'conversation_id' => $conversationId,
            'rcs_user_id' => $rcsUserId,
            'message_type' => $type,
            'direction' => $direction,
            'content' => $content,
            'rcs_message_id' => $rcsMessageId,
            'error' => $error ? $error : "NULL"
        ]);
        return $messageId;  
    }
    
    private function updateMessagesStatus($messageId, $status)
    {
        return $this->db->update($this->messagesTable, ['status' => $status], "rcs_message_id = '$messageId'");
    }
    
    
}
