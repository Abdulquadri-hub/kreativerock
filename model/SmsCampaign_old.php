<?php

require_once 'SmsIntegration.php';
require_once 'Conversation.php';

class SmsCampaign {
    
    private $model;
    private $smsIntegration;
    private $twoWaySms;
    private $db;
    private $conversation;
    private $campaignTable = 'sms_campaigns';
    private $messagesTable = 'messages';
    private $campaignMessagesTable = 'campaign_messages';
    private $keywordResponsesTable = 'keyword_responses';
    private $promptsResponsesTable = 'prompts_responses';
    private $rcsUsersTable = 'rcs_users';
    private $conversationsTable = 'conversations';
    private $usersTable = 'users';
    public $validatedData;
    public $errors = [];
    
    public function __construct() {
        $this->model = new Model();
        $this->db = new dbFunctions();
        $this->smsIntegration = new SmsIntegration();
        $this->twoWaySms = new TwoWaySms();
        $this->conversation = new Conversation();
    }
    
    public function validate($postData){
        $this->errors = []; 
        $this->validatedData = $this->sanitizedValidation($postData);
        return empty($this->errors);
    }
    
    public function getValidatedData(){
        return $this->validatedData;
    }
    
    public function validationErrors(){
        return $this->errors;
    }
    
    public function checkMessageStatus($messageId) {
        return $this->smsIntegration->checkMessageStatus($messageId);
    }
    
    public function checkRcsCapability($phoneNumber) {
        return $this->smsIntegration->checkRcsCapability($phoneNumber);
    }
    
    public function deleteCampaign($campaignId, $email) {
        try {
           
            $this->db->beginTransaction();
            
            $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");
            if (!$campaign) {
                return ['status' => false, 'message' => 'Campaign not found'];
            }
            
            $sender = $this->db->find($this->usersTable, "email = '$email'");
            if(!$sender){
                return ['status' => false, 'message' => 'User not found'];
            }
            
            $this->db->delete($this->promptsResponsesTable, "campaign_id = '$campaignId' AND user_id = '{$sender['id']}'");
            $this->db->delete($this->keywordResponsesTable, "campaign_id = '$campaignId' AND user_id = '{$sender['id']}'");
            
            $campaignConversationUsers = $this->conversation->getCampaignUsersList($campaignId, $email);
            
            $processedConversations = [];
            $processedRcsUsers = [];
            $processedMessages = [];
            
            foreach($campaignConversationUsers as $campaignConversationUser){
               $results = $this->conversation->getMessagesForUser($campaignId, $campaignConversationUser['phone_number'], $email);

               if (!empty($results)) {
                   
                   foreach($results as $row){
                       
                       if(!empty($row)){
                            if (!empty($row['message_id']) && !in_array($row['message_id'], $processedMessages)) {
                               $processedMessages[] = $row['message_id'];
                            }
                           
                            if (!empty($row['conversation_id']) && !in_array($row['conversation_id'], $processedConversations)) {
                               $processedConversations[] = $row['conversation_id'];
                            }
                           
                            if (!empty($row['rcs_user_id']) && !in_array($row['rcs_user_id'], $processedRcsUsers)) {
                              $processedRcsUsers[] = $row['rcs_user_id'];
                            }
                       }
                   }
                   
                

                    if (!empty($processedConversations)) {
                      $conversationIds = implode(',', $processedConversations);
                      if($conversationIds){
                          $this->db->delete($this->conversationsTable, "conversation_id  IN ($conversationIds)");
                      }
                    }
                
                    if (!empty($processedRcsUsers)) {
                        $rcsUserIds = implode(',', $processedRcsUsers);
                        if($rcsUserIds){
                            $this->db->delete($this->rcsUsersTable, "id IN ($rcsUserIds)");
                        }
                    }
                
                    if (!empty($processedMessages)) {
                        $messageIds = implode(',', $processedMessages);
                        if($messageIds){
                            $this->db->delete($this->messagesTable, "message_id IN ($messageIds)  AND user_id = '{$sender['id']}'");
                        }
                        
                    }
                   
               }
            }

            $this->db->delete($this->campaignTable, "id = '$campaignId'  AND user_id = '{$sender['id']}'");
            $this->db->delete($this->campaignMessagesTable, "campaign_id = '$campaignId'");
            
            $this->db->commitTransaction();
            
            return [
                'status' => true, 
                'message' => 'Campaign and associated data deleted successfully',
                'deleted_data' => [
                    'messages' => count($processedMessages),
                    'conversations' => count($processedConversations),
                    'rcs_users' => count($processedRcsUsers)
                ]
            ];
            
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
            error_log($e->getMessage());
            return ['status' => false, 'message' => 'Error deleting campaign: ' . $e->getMessage()];
        }
    }
    
    public function launchCampaign($campaignId, $email){
        $result = $this->db->find($this->usersTable, "email = '$email'");
        if(count($result) < 0 || empty($result)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        
        $campaignExists = $this->db->find($this->campaignTable, "id = '$campaignId' AND user_id = '{$result['id']}' AND status = 'draft'");
        if(!$campaignExists){
            return ['status' => false, 'code' => 404, 'message' => 'Camapign not found.'];
        }
        
        $phoneNumbers = $campaignExists['phone_numbers'];
        $requiredUnits = count((array)$phoneNumbers); 
        if (!$this->smsIntegration->deductUnits($email, $requiredUnits)) {
             return ['status' => false, 'code' => 442, 'message' => 'Insufficient SMS units. Please recharge your account'];
             exit;
        }
        
        return $this->executeImmediateMessage($campaignId);
        
    }

    public function getCampaign($request){
        $campaignId = isset($request['campaign_id']) && $request['campaign_id'] !== ""  ?  $request['campaign_id'] :  "";
        $startDate = isset($request['start_date']) && $request['start_date'] !== "" ?  $request['start_date'] :  "";
        $endDate = isset($request['end_date']) && $request['end_date'] !== "" ?  $request['end_date'] :  date("Y-m-d H:i:s");
        $status = isset($request['status']) && $request['status'] !== "" ?  $request['status'] :  "";
        $email = $request['email'];
        
        $sender = $this->db->find($this->usersTable, "email = '$email'");
        if(!$sender){
            return ['status' => false, 'message' => 'User not found'];
        }

        if(!empty($campaignId)){
            if(!empty($startDate) && !empty($status)){
                $campaigns = $this->db->select($this->campaignTable, "*", "id = '$campaignId' AND created_at BETWEEN $startDate AND $endDate AND status = '$status' AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }
            }
            elseif(!empty($startDate)){
                $campaigns =  $this->db->select($this->campaignTable, "*", "id = '$campaignId' AND created_at BETWEEN $startDate AND $endDate AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }                
            }
            elseif(!empty($status)){
                $campaigns = $this->db->select($this->campaignTable, "*", "id = '$campaignId' AND status = '$status' AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }                
            }
            else{
                $campaigns =  $this->db->select($this->campaignTable, "*", "id = '$campaignId' AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }                
            }
        }else{
            if(!empty($startDate) && !empty($status)){
                $campaigns =  $this->db->select($this->campaignTable, "*", "created_at BETWEEN $startDate AND $endDate AND status = '$status' AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }
            }
            elseif(!empty($startDate)){
                $campaigns =  $this->db->select($this->campaignTable, "*", "created_at BETWEEN $startDate AND $endDate AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }
            }
            elseif(!empty($status)){
                $campaigns = $this->db->select($this->campaignTable, "*", "status = '$status' AND user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }
            }
            else{
                $campaigns =  $this->db->select($this->campaignTable, "*", "user_id = '{$sender['id']}'", 'id DESC');
                if(!empty($campaigns)){
                    foreach($campaigns as $key => &$campaign){
                        if($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated"){
                            $campaignId = $campaign['id'];
                            $campaign['prompts'] = $this->db->select($this->promptsResponsesTable, "*", "campaign_id = '$campaignId' AND user_id = '{$sender['id']}'");
                        }
                    }
                    return $campaigns;
                }else{
                    return null;
                }
            }
        }
    }
    
    public function sendImmediateMessage($email, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction) {
        $result = $this->db->find($this->usersTable, "email = '$email'");
        if(count($result) < 0 || empty($result)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
            exit;
        }
        
        
        if($submitaction == "draft"){
           $campaignId = $this->createOrUpdateCampaign($email, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction);
           if(!empty($campaignId)){
               return ['status' => true, 'code' => 201, 'message' => 'campaign saved as draft'];
                exit;
           }
        }
        
        $requiredUnits = count((array)$phoneNumbers); 
        if (!$this->smsIntegration->deductUnits($email, $requiredUnits)) {
             return ['status' => false, 'code' => 442, 'message' => 'Insufficient SMS units. Please recharge your account'];
             exit;
        }
        
        $campaignId = $this->createOrUpdateCampaign(
            $email, $campaignName, 
            $phoneNumbers, $filteredMessage, 
            $scheduleDate, $repeatInterval, 
            $smsPages, $type, 
            $responseHandling, $prompts,
            $submitaction
        );
        
        return $this->executeImmediateMessage($campaignId);
    }
    
    public function sendScheduleMessage($email, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction) {
        $result = $this->db->find($this->usersTable, "email = '$email'");
        if(count($result) < 0 || empty($result)){
            return [
                'status' => false, 
                'code' => 404, 
                'message' => 'User not found.'
            ];
            exit;
        }
        $scheduleDate = date('Y-m-d H:i:s', strtotime($scheduleDate));
        
        if (!empty($scheduleDate)) {
            $scheduleTime = strtotime($scheduleDate);
            $currentTime = time();

            if ($scheduleTime > $currentTime) {
                if ($repeatInterval !== 'NO REPEAT') {
                    $campaignName = $campaignName;
                }
                
                if($submitaction == "draft"){
                   $campaignId = $this->createOrUpdateCampaign($email, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction);
                   return ['status' => true, 'code' => 201, 'message' => 'campaign saved as draft'];
                   exit;
                }
                
                $campaignId = $this->createOrUpdateCampaign($email, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction);
                if(!empty($campaignId)){
                    return [
                        "status" => "true",
                        "message" => "Scheduled campaign created successfully",
                    ];
                }
                // return $this->executeScheduleMessage($campaignId);
            } else {
                return null;
            }
        }
    }

    public function updateCampaign($email, $campaignId, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction){
        $result = $this->db->find($this->usersTable, "email = '$email'");
        if(count($result) < 0 || empty($result)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        
        $campaignData = [
            'name' => $campaignName,
            'phone_numbers' => $phoneNumbers,
            'message' => $filteredMessage,
            'status' => $submitaction == "draft" ? 'draft' : 'draft',
            'scheduled_date' => $scheduleDate,
            'repeat_interval' => $repeatInterval,
            'sms_pages' => $smsPages,
            'type' => $type,
            'response_handling' => $responseHandling
        ];
        
        $existingCampaign = $this->db->find($this->campaignTable, "id = '$campaignId' AND status = 'draft' AND user_id = '{$result['id']}'");

        if ($existingCampaign) {
            $this->db->update($this->campaignTable, $campaignData, "id = '$campaignId'");
            
            if(!empty($campaignId) && !is_null($prompts)){
                $this->handlePrompts($campaignId, $result['id'], $type, $responseHandling, $prompts);
                return ['status' => true, 'message' => 'Campaign updated successfully'];
            }else{
                if(!empty($campaignId)){
                    return ['status' => true, 'message' => 'Campaign updated successfully'];
                }
            }
        }else{
            return ['status' => false, 'message' => 'Campaign do not exists.'];
        }
    }
    
    private function createOrUpdateCampaign($email, $campaignName, $phoneNumbers, $filteredMessage, $scheduleDate, $repeatInterval, $smsPages, $type, $responseHandling, $prompts, $submitaction) {
        $result = $this->db->find($this->usersTable, "email = '$email'");
        if(count($result) < 0 || empty($result)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        
        $campaignData = [
            'user_id' => $result['id'],
            'name' => $campaignName,
            'phone_numbers' => $phoneNumbers,
            'message' => $filteredMessage,
            'status' => $submitaction == "draft" ? 'draft' : 'draft',
            'scheduled_date' => $scheduleDate,
            'repeat_interval' => $repeatInterval,
            'sms_pages' => $smsPages,
            'type' => $type,
            'response_handling' => $responseHandling
        ];
        
        $existingCampaign = $this->db->find($this->campaignTable, "user_id = '{$result['id']}' AND status = 'draft'");
        
        if ($existingCampaign) {
            $campaignId = $existingCampaign['id'];
            $this->db->update($this->campaignTable, $campaignData, "id = '$campaignId'");
        } else {
            $campaignId = $this->db->insert($this->campaignTable, $campaignData);
        }
        
        if(!empty($campaignId) && !is_null($prompts)){
            $this->handlePrompts($campaignId, $result['id'], $type, $responseHandling, $prompts);
            return $campaignId;
        }else{
            if(!empty($campaignId)){
                return $campaignId;
            }
        }
        
        return ['status' => false, 'code' => 500, 'message' => 'Failed to create or update campaign'];
    }
    
    private function handlePrompts($campaignId, $userId, $type, $responseHandling, $prompts) {

        $promptsData = [];
        foreach ($prompts as $prompt) {
            $promptData = [
                'prompt' => $prompt["prompt"],
                'campaign_id' => $campaignId,
                'user_id' => $userId,
                'expected_response' => $prompt["expectedResponse"],
                'response_message' => $prompt["response"],
                'expected_response_type' => $prompt["expectedResponsetype"],
                'next_prompt_id' => $prompt["next_prompt_id"] ?? NULL
            ];

            if ($type === "promotional" && $responseHandling === "automated"){
                $existingPrompt = $this->db->find($this->promptsResponsesTable, "campaign_id = '$campaignId' AND user_id = '$userId'");
                if($existingPrompt){
                    $this->db->update($this->promptsResponsesTable, $promptData, "id = '{$existingPrompt['id']}'");
                }else{
                    $this->db->insert($this->promptsResponsesTable, $promptData);
                }
            }
            elseif($type === "keyword" && $responseHandling === "manaul"){
                $existingPrompt = $this->db->find($this->keywordResponsesTable, "campaign_id = '$campaignId' AND user_id = '$userId'");
                if($existingPrompt){
                    $this->db->update($this->keywordResponsesTable, $promptData, "id = '{$existingPrompt['id']}'");
                }else{
                    $this->db->insert($this->keywordResponsesTable, $promptData);
                }
            }
        }
    }
    
    private function executeImmediateMessage($campaignId) {
        
        $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");
        $targetAudience = json_decode($campaign['phone_numbers'], true);

        $results = $this->smsIntegration->sendBulkOneWaySms($targetAudience, $campaign['message']);

        $responses = [];
        foreach ($results as $phoneNumber => $result) {
            $this->handleMessage($campaignId, $campaign['user_id'], $result, $campaign['message'], $phoneNumber);
            
            $decodedResponse = $result->getDecodedResponse();

            if ($decodedResponse) {
                $status = $decodedResponse['data']['RCSMessage']['status'] ?? null;
                $messageId = $decodedResponse['data']['RCSMessage']['msgId'] ?? null;
                $code = $decodedResponse['code'] ?? null;
        
               $responses[] = [
                   'status' => $status,
                   'messageId' => $messageId,
                   'code' => $code
                ];
            }
        }
        
        if(empty($responses)){
            return ['status' => false, 'message' => 'Error executing campaign.', 'data' => $responses];
            exit;
        }
        $this->db->update($this->campaignTable, ['status' => 'completed'], "id = '$campaignId'");
        return ['status' => true, 'code' => 200, 'message' => 'Campaign executed successfully', 'data' => $responses];
    }
    
    private function sanitizedValidation($postData) {
        $sanitizedData = [];
        $this->errors = [];
        
        if (!isset($postData['submitaction']) || empty($postData['submitaction'])) {
            $this->errors['submitaction'] = "Submitaction is required.";
        } else {
            $sanitizedData['submitaction'] = $postData['submitaction'];
        }
    
        if (!isset($postData['campaignname']) || empty($postData['campaignname'])) {
            $this->errors['campaignname'] = "Campaign name is required.";
        } else {
            $sanitizedData['campaignname'] = filter_var($postData['campaignname'], FILTER_SANITIZE_STRING);
        }

        if (!isset($postData['contacts']) || empty($postData['contacts'])) {
            $this->errors['contacts'] = "Contacts are required.";
        } else {
            $phoneNumbers = explode(",", $postData['contacts']);
            $sanitizedPhoneNumbers = array_map(function($number) {
            return preg_replace('/[^0-9+]/', '', $number);
            }, $phoneNumbers);
            $sanitizedData['contacts'] = json_encode($sanitizedPhoneNumbers);
            
        }

        if (!isset($postData['campaignmessage']) || empty($postData['campaignmessage'])) {
            $this->errors['campaignmessage'] = "Campaign message is required.";
        }elseif(strlen($postData['campaignmessage']) > 160){
            $this->errors['campaignmessage'] = "Campaign message must not exceed 160 characters.";
        }else {
            $sanitizedData['campaignmessage'] = filter_var($postData['campaignmessage'], FILTER_SANITIZE_STRING);
        }

        if (isset($postData['scheduledate']) && !empty($postData['scheduledate'])) {
            $sanitizedData['scheduledate'] = date('Y-m-d H:i:s', strtotime($postData['scheduledate']));
        } else {
            $sanitizedData['scheduledate'] = null;
        }

        if (isset($postData['repeatinterval']) && !empty($postData['repeatinterval'])) {
            $sanitizedData['repeatinterval'] = filter_var($postData['repeatinterval'], FILTER_SANITIZE_STRING);
        } else {
            $sanitizedData['repeatinterval'] = null;
        }

        if (isset($postData['smspages']) && !empty($postData['smspages'])) {
            $sanitizedData['smspages'] = filter_var($postData['smspages'], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $sanitizedData['smspages'] = null;
        }

        if (!isset($postData['campaigntype']) || empty($postData['campaigntype'])) {
            $this->errors['campaigntype'] = "Campaign type is required.";
        } else {
            $allowedTypes = ['promotional', 'keyword', 'transactional'];
            if (in_array($postData['campaigntype'], $allowedTypes)) {
                $sanitizedData['campaigntype'] = $postData['campaigntype'];
            } else {
                $this->errors['campaigntype'] = "Invalid campaign type.";
            }
        }

        if (!isset($postData['responsehandling']) || empty($postData['responsehandling'])) {
                $this->errors['responsehandling'] = "Response handling is required.";
        } else {
            $allowedResponseHandling = ['automated', 'manual'];
            if (in_array($postData['responsehandling'], $allowedResponseHandling)) {
                $sanitizedData['responsehandling'] = $postData['responsehandling'];
            } else {
                $this->errors['responsehandling'] = "Invalid response handling.";
            }
        }

        if(($sanitizedData['campaigntype'] === "promotional" && $sanitizedData['responsehandling'] === "automated") 
            || ($sanitizedData['campaigntype'] === "keyword" && $sanitizedData['responsehandling'] === "manual")
          ){
              
            if (isset($postData['promptsrows'])) {

            $promptRows = intval($postData['promptsrows']);
            $sanitizedData['promptsrows'] = $promptRows;
            
            for ($i = 0; $i < $promptRows; $i++) {

                if (!isset($postData["prompt{$i}"]) || empty($postData["prompt{$i}"])) {
                    $this->errors["prompt{$i}"] = "Prompt {$i} is required.";
                } else {
                    $sanitizedData["prompt{$i}"] = filter_var($postData["prompt{$i}"], FILTER_SANITIZE_STRING);
                }
                
                if (!isset($postData["expectedresponse{$i}"]) || 
                    empty($postData["expectedresponse{$i}"])) {
                    $this->errors["expectedresponse{$i}"] = "Expected response for prompt {$i} is required.";
                } else {
                    $sanitizedData["expectedresponse{$i}"] =  filter_var($postData["expectedresponse{$i}"], FILTER_SANITIZE_STRING);
                }
                
                if (!isset($postData["response{$i}"]) || empty($postData["response{$i}"])) {
                    $this->errors["response{$i}"] = "Response for prompt {$i} is required.";
                } else {
                    $sanitizedData["response{$i}"] =  filter_var($postData["response{$i}"], FILTER_SANITIZE_STRING);
                }
                
                
                if (!isset($postData["expectedResponsetype{$i}"]) || empty($postData["expectedResponsetype{$i}"])) {
                    $this->errors["expectedResponsetype{$i}"] =  "Expected response type for prompt {$i} is required.";
                } else {
                    $sanitizedData["expectedResponsetype{$i}"] =  filter_var($postData["expectedResponsetype{$i}"], FILTER_SANITIZE_STRING);
                }
            }
        }
        }
        

        return $sanitizedData;
    }
    
    private function executeScheduleMessage($campaignId) {
        $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");
        $targetAudience = json_decode($campaign['phone_numbers'], true);

        $results = $this->smsIntegration->sendBulkOneWaySms($targetAudience, $campaign['message']);
        $responses = [];
        foreach ($results as $phoneNumber => $result) {
            $this->handleMessage($campaignId, $campaign['user_id'], $result, $campaign['message'], $phoneNumber);
            
            $decodedResponse = $result->getDecodedResponse();

            if ($decodedResponse) {
                $status = $decodedResponse['data']['RCSMessage']['status'] ?? null;
                $messageId = $decodedResponse['data']['RCSMessage']['msgId'] ?? null;
                $code = $decodedResponse['code'] ?? null;
        
               $responses[] = [
                   'status' => $status,
                   'messageId' => $messageId,
                   'code' => $code
                ];
            }
        }
        if(empty($responses)){
            return ['status' => false, 'message' => 'Error executing campaign.', 'data' => $responses];
            exit;
        }
        $this->db->update($this->campaignTable, ['status' => 'completed'], "id = '$campaignId'");
        return ['status' => true, 'code' => 200, 'message' => 'Campaign executed successfully', 'data' => $responses];
    }
    
    private function handleMessage($campaignId, $senderId, $result, $message, $phoneNumber) {

        if ($result->isSuccess()) {
            $messageStatus = $result->getMessageStatus();
            $messageId = $result->getMessageId();
            $error = null;
        } else {
            $messageStatus = $result->getMessageStatus();
            $messageId = $result->getMessageId();
            $error = $result->getMessage();
        }
        $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");

        $insertedMessageId = $this->insertMessage(
            $senderId,
            null, 
            null, 
            $phoneNumber, 
            'text', 
            'outgoing', 
            $message, 
            $messageId, 
            $error, 
            $campaign['type']
        );
        if (!empty($insertedMessageId)) {
            $this->db->insert($this->campaignMessagesTable, [
                'campaign_id' => $campaignId,
                'message_id' => $insertedMessageId,
                'position' => 'first',
            ]);
        }
    }
    
    private function insertMessage($senderId, $conversationId, $rcsUserId, $destinations, $type, $direction, $content, $rcsMessageId = null, $error = null, $interactionType = null) {
        $messageId = $this->db->insert($this->messagesTable, [
            'user_id' => $senderId,
            'conversation_id' => $conversationId,
            'rcs_user_id' => $rcsUserId,
            'message_type' => $type,
            'destinations' => json_encode($destinations),
            'direction' => $direction,
            'content' => $content,
            'interaction_type' => $interactionType ? $interactionType : 'prompt',
            'rcs_message_id' => $rcsMessageId,
            'error' => $error ? $error : "NULL"
        ]);
        return $messageId;  
    }
    
}