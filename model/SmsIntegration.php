<?php

require_once 'DotgoApi.php';

class SmsIntegration {
    private $api;
    private $db;
    private $messageLogsTable = 'message_logs';
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
                $responses[$phoneNumber] = $response;
            } else {
                $responses[$phoneNumber] = $response->getErrorDetails();
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
            return [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function getDashboardStats($email) {
        $stats = [
            'total_unit_spent' => $this->getTotalSpentUnitsQty($email),
            'unit_balance' => $this->getTotalPurchasedUnitsQty($email),
            'total_registered_users' => $this->getTotalRegisteredUsers(),
            'total_online_users' => $this->getTotalOnlineUsers(),
            'total_sms_accounts' => $this->getTotalSmsAccounts(),
            'total_whatsapp_accounts' => $this->getTotalWhatsappAccounts(),
            'whatsapp_unit_balance' => $this->getTotalWhatsappPurchasedUnitsQty($email),
            'total_whatsappunit_spent' => $this->getWhatsappTotalSpentUnitsQty($email)
        ];
        return $stats;
    }
    
    public function getTotalPurchasedUnitsQty($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $totalPurchasedUnits = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'sms'");
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
        
        $unit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'sms'");
        if(!$unit){
            return 0;
        }
        else{
            if($unit['total_used_qty'] == 0){
                // return $this->getTotalPurchasedUnitsQty($email);
                return $unit['total_unit_balance'];
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
        
        $totalSpentUnits = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'sms'");
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
        
        $currentBalance = $this->getTotalUnitsBalance($email);
echo json_encode($currentBalance);
        if ($currentBalance < $unitsToDeduct) {
            return false;
        }

        $totalUsed = $this->getTotalSpentUnitsQty($email) + $unitsToDeduct;
        $newBalance = $currentBalance - $unitsToDeduct;

        $userUnit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'sms'");
        if (empty($userUnit)) {
            return false;
        }

        $this->db->update($this->userUnitsTable, [
            "total_used_qty" => $totalUsed,
            "total_unit_balance" => $newBalance
        ], "user_id = '{$user['id']}' AND type = 'sms'");
    
        return true;
    }

    /** SMS ENDs */



    /**   whatsapp   */

    public function getTotalWhatsappPurchasedUnitsQty($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $totalPurchasedUnits = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'whatsapp'");
        if(!$totalPurchasedUnits){
            return 0;
        }
        else{
            return $totalPurchasedUnits['total_purchased_qty'];
        }
    }
    
    public function deductWhatsappUnits($email, $unitsToDeduct) {
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return false;
        }
        
        $currentBalance = $this->getWhatsappTotalUnitsBalance($email);

        if ($currentBalance < $unitsToDeduct) {
            return false;
        }

        $totalUsed = $this->getWhatsappTotalSpentUnitsQty($email) + $unitsToDeduct;
        $newBalance = $currentBalance - $unitsToDeduct;

        $userUnit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'whatsapp'");
        if (empty($userUnit)) {
            return false;
        }

        $this->db->update($this->userUnitsTable, [
            "total_used_qty" => $totalUsed,
            "total_unit_balance" => $newBalance
        ], "user_id = '{$user['id']}' AND type = 'whatsapp'");
    
        return true;
    }

    public function getWhatsappTotalSpentUnitsQty($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $totalSpentUnits = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'whatsapp'");
        if(!$totalSpentUnits){
            return 0;
        }
        else{
            return $totalSpentUnits['total_used_qty'];
        } 
    }

    public function getWhatsappTotalUnitsBalance($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        $unit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'whatsapp'");
        if(!$unit){
            return 0;
        }
        else{
            if($unit['total_used_qty'] == 0){
                // return $this->getTotalPurchasedUnitsQty($email);
                return $unit['total_unit_balance'];
            }
            else{
                return $unit['total_unit_balance'];
            }
        }
    }

    
    /** Whatsapp Ends */

    public function getUserUnitsInfo($email){
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }

        return [
            "status" => true,
            "code" => 200,
            "unit_balance" => $this->getTotalUnitsBalance($email),
            "whatsappunit_balance" => $this->getWhatsappTotalUnitsBalance($email),
            "unit_spent" => $this->getTotalSpentUnitsQty($email),
            "whatsappunit_spent" => $this->getWhatsappTotalSpentUnitsQty($email)
        ];
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
           return $response->getData();
        } else {
            return $response->getData();
        }
    }
    
    private function getTotalRegisteredUsers() {
        $users =  $this->db->select('users', "*");
        if(is_array($users)){
            return count($users) ?? 0;
        }
    }

    private function getTotalOnlineUsers() {
        $users =  $this->db->select('users', "*");
        if(is_array($users)){
            return count($users) ?? 0;
        }
    }

    private function getTotalSmsAccounts() {
        $users =  $this->db->select('users', "*");
        if(is_array($users)){
            return count($users) ?? 0;
        }
    }

    private function getTotalWhatsappAccounts() {
        return 0;
    }    
}
