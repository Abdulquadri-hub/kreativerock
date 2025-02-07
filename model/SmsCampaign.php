<?php

require_once 'SmsIntegration.php';
require_once 'Conversation.php';

class SmsCampaign {
    private $db;
    private $smsIntegration;
    private $conversation;
    private $campaignTable = 'sms_campaigns';
    private $messagesTable = 'messages';
    private $contactsTable = 'contacts';
    private $conversationsTable = 'conversations';
    private $conversationPromtsTable = 'conversation_prompts';
    private $usersTable = 'users';
    public $errors = [];
    
    public function __construct() {
        $this->db = new dbFunctions();
        $this->smsIntegration = new SmsIntegration();
        $this->conversation = new Conversation();
    }
    
    public function createCampaign($params, $email) {
       
        if (!$this->validateParams($params)) {
            return ['status' => false, 'message' => 'Missing required fields', 'errors' => $this->errors];
        }
       
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if (!$user) {
            return ['status' => false, 'message' => 'User not found'];
        }

        $phoneNumbers = [];
        $contacts = $this->db->select($this->contactsTable, "*", "segment_id = {$params['segment_id']}");

        foreach ($contacts as $key => $contact) {
            if($params['channel'] === "sms"){
                if($contact['type'] == "TEXT" || $contact['FILE']){
                    $phoneNumbers[] = $contact['sms'];
                }
                else{
                    $phoneNumbers[] = $contact['sms'];
                }
            }
            else{
                if($contact['type'] == "TEXT" || $contact['FILE']){
                    $phoneNumbers[] = $contact['whatsapp'];
                }
                else{
                    $phoneNumbers[] = $contact['whatsapp'];
                }
            }
        }
        
        $campaignData = [
            'user_id' => $user['id'],
            "channel" =>  $params['channel'],
            'name' => $params['campaignname'],
            'type' => $params['campaigntype'],
            'message' => $params['campaignmessage'],
            'phone_numbers' => json_encode($phoneNumbers),
            'sms_pages' => $params['smspages'],
            'status' => 'draft',
            'scheduled_date' => $params['scheduled'] === 'NOW' ? null : $params['scheduledDate'],
            'repeat_interval' => $params['repeatcampaign'],
            'response_handling' => $params['responsehandling'] ?? 'manual'
        ];
        
        $existingCampaign = $this->db->find($this->campaignTable, "user_id = '{$user['id']}' AND status = 'draft'");
        
        if ($existingCampaign) {
            $campaignId = $existingCampaign['id'];
            $this->db->update($this->campaignTable, $campaignData, "id = '$campaignId'");
            return [
                'status' => true,
                'message' => 'Campaign updated successfully',
                'campaign_id' => $campaignId,
            ];

        } else {
            $campaignId = $this->db->insert($this->campaignTable, $campaignData);

            if($params['campaigntype'] ===  "promotional" && $params['responsehandling'] === "automated"){
                $this->handlePrompts($campaignId, $user['id'], $params['prompts']);
            }

            if(!empty($campaignId)){
    
                if ($params['scheduled'] === 'NOW') {
                    return $this->sendNow($campaignId, $email);
                }

                return [
                    'status' => true,
                    'message' => 'Campaign created successfully',
                    'campaign_id' => $campaignId,
                ];
    
            }
            else {
                return [
                    'status' => false,
                    'message' => 'Error creating campaign',
                    'campaign_id' => $campaignId,
                ];
            }
        } 
    }
    
    public function getCampaign($request) {
        
        $campaignId = $request['campaign_id'] ?? "";
        $startDate = $request['start_date'] ?? "";
        $endDate = $request['end_date'] ?? date("Y-m-d H:i:s");
        $status = $request['status'] ?? "";
        $email = $request['email'];
        
        $sender = $this->db->find($this->usersTable, "email = '$email'");
        if (!$sender) {
            return ['status' => false, 'message' => 'User not found'];
        }

        $whereConditions = ["user_id = '{$sender['id']}'"];
        
        if ($campaignId) {
            $whereConditions[] = "id = '$campaignId'";
        }
        if ($startDate) {
            $whereConditions[] = "created_at BETWEEN '$startDate' AND '$endDate'";
        }
        if ($status) {
            $whereConditions[] = "status = '$status'";
        }

        $whereClause = implode(" AND ", $whereConditions);
        $campaigns = $this->db->select($this->campaignTable, "*", $whereClause, 'id DESC');

        if (!empty($campaigns)) {
            foreach ($campaigns as &$campaign) {
                if ($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated") {
                    $campaign['prompts'] = $this->db->select(
                        $this->promptsResponsesTable, 
                        "*", 
                        "campaign_id = '{$campaign['id']}'"
                    );
                }
            }
        }

        return $campaigns ?: null;
    }

    public function deleteCampaign($campaignId, $email) {
        try {
            $this->db->beginTransaction();
            
            $sender = $this->db->find($this->usersTable, "email = '$email'");
            if (!$sender) {
                return ['status' => false, 'message' => 'User not found'];
            }

            $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");
            if (!$campaign) {
                return ['status' => false, 'message' => 'Campaign not found'];
            }

            $this->db->delete($this->promptsResponsesTable, 
                "campaign_id = '$campaignId' AND user_id = '{$sender['id']}'"
            );
            $this->db->delete($this->keywordResponsesTable, 
                "campaign_id = '$campaignId' AND user_id = '{$sender['id']}'"
            );

            $campaignConversationUsers = $this->conversation->getCampaignUsersList($campaignId, $email);
            foreach ($campaignConversationUsers as $user) {
                $messages = $this->conversation->getMessagesForUser(
                    $campaignId, 
                    $user['phone_number'], 
                    $email
                );
                
                if ($messages) {
                    $this->deleteAssociatedData($messages, $sender['id']);
                }
            }

            $this->db->delete($this->campaignTable, 
                "id = '$campaignId' AND user_id = '{$sender['id']}'"
            );
            $this->db->delete($this->campaignMessagesTable, 
                "campaign_id = '$campaignId'"
            );

            $this->db->commitTransaction();
            return ['status' => true, 'message' => 'Campaign deleted successfully'];

        } catch (Exception $e) {
            $this->db->rollbackTransaction();
            return ['status' => false, 'message' => 'Error deleting campaign: ' . $e->getMessage()];
        }
    }

    private function validateParams($params) {
        $this->errors = [];
        
        if (empty($params['segment_id'])) {
            $this->errors['segment_id'] = 'Segment id is required';
        }

        if (empty($params['channel'])) {
            $this->errors['channel'] = 'Channel is required';
        }

        if (empty($params['campaignname'])) {
            $this->errors['campaignname'] = 'Campaign name is required';
        }
        
        if (empty($params['campaignmessage'])) {
            $this->errors['campaignmessage'] = 'Message is required';
        } elseif (strlen($params['campaignmessage']) > 160) {
            $this->errors['campaignmessage'] = 'Message cannot exceed 160 characters';
        }

        if (empty($params['campaigntype'])) {
            $this->errors['campaigntype'] = 'Campaign type is required';
        }

        if ($params['campaigntype'] === "promotional") {
            if(!isset($params['responsehandling']) || empty($params['responsehandling'])){
                $this->errors['responsehandling'] = 'Response Handling for the campaign type is required';
            }
            else {
                $allowedResponseHandling = ['automated', 'manual'];
                if (in_array($params['responsehandling'], $allowedResponseHandling)) {
                    $sanitizedData['responsehandling'] = $params['responsehandling'];
                } else {
                    $this->errors['responsehandling'] = "Invalid response handling.";
                }
            }
        }
        
        if ($params['scheduled'] !== 'NOW' && empty($params['scheduledDate'])) {
            $this->errors['scheduledDate'] = 'Schedule date is required for scheduled campaigns';
        }

        // if($params['campaigntype'] === "promotional" && $params['responsehandling'] === "automated"){
        //     if(!isset() || empty($params['prompts']['']));
        // }
        
        return empty($this->errors);
    }
    
    private function sendNow($campaignId, $email) {
        
        $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");
        if (!$campaign) {
            return ['status' => false, 'message' => 'Campaign not found'];
        }
        
        $phoneNumbers = json_decode($campaign['phone_numbers'], true);
        $requiredUnits = count($phoneNumbers);
        

        if (!$this->smsIntegration->deductUnits($email, $requiredUnits)) {
            return ['status' => false, 'message' => 'Insufficient SMS units'];
        }
        
        $results = $this->smsIntegration->sendBulkOneWaySms($phoneNumbers, $campaign['message']);
       
        $conversationId = null;
        if (($campaign['type'] === "promotional" && $campaign['response_handling'] === "automated")) {
            $conversationId = $this->conversation->startConversation($campaign['id']);
        }
        
        $responses = [];

        foreach ($results as $phoneNumber => $result) {
            $messageId = $this->saveMessage($campaign['user_id'], $phoneNumber, $campaign,$conversationId, $result);
            
            if ($messageId) {
                $responses[] = [
                    'phone' => $phoneNumber,
                    'status' => $result->isSuccess() ? 'sent' : 'failed',
                    'message_id' => $result->getMessageId(),
                    'campaign_id' => $campaign['id'],
                    'error' => $result->isSuccess() ? null : $result->getMessage()
                ];
            }
            else{
                $responses[] = [
                    'status' => $result->isSuccess() ? 'sent' : 'failed',
                    'error' => $result->isSuccess() ? null : $result->getMessage()
                ];
            }
            
        }
        
        $this->db->update($this->campaignTable, ['status' => 'completed'], "id = '$campaignId'");
        
        return [
            'status' => true,
            'message' => 'Campaign sent successfully',
            'data' => $responses
        ];
    }
    
    private function handlePrompts($campaignId, $creatorId, $prompts) {
        foreach ($prompts as $prompt) {
            $promptData = [
                'campaign_id' => $campaignId,
                'user_id' => $creatorId,
                'message_text' => $prompt['prompt'],
                'response_type' => $this->mapResponseType($prompt['responsetype']),
                'response_value' => $prompt['response'],
                'next_prompt_id' => $prompt['nextpromtid'] ?? null,
                'parent_prompt_id' => $prompt['parentpromptid'] ?? null,
                'is_end_prompt' => isset($prompt['isendprompt']) ? $prompt['isendprompt'] : 0
            ];

            $existingPrompt = $this->db->find($this->conversationPromtsTable,"campaign_id = '$campaignId' AND message_text = '{$prompt['prompt']}' AND response_type = '{$this->mapResponseType($prompt['responsetype'])}'");
            
            if ($existingPrompt) {
                $this->db->update($this->conversationPromtsTable,$promptData,"id = '{$existingPrompt['id']}'");
                $this->updatePromptReferences($existingPrompt['id'], $promptData['next_prompt_id']);
            } else {
                $promptId = $this->db->insert($this->conversationPromtsTable, $promptData);
                
                if (!isset($prompt['parentpromptid'])) {
                    $this->conversation->startConversation($campaignId, $promptId);
                }
            }
        }
    }

    private function deleteAssociatedData($messages, $userId) {
        $conversationIds = [];
        $rcsUserIds = [];
        $messageIds = [];

        foreach ($messages as $message) {
            if (!empty($message['conversation_id'])) {
                $conversationIds[] = $message['conversation_id'];
            }
            if (!empty($message['rcs_user_id'])) {
                $rcsUserIds[] = $message['rcs_user_id'];
            }
            if (!empty($message['message_id'])) {
                $messageIds[] = $message['message_id'];
            }
        }

        if ($conversationIds) {
            $ids = implode(',', array_unique($conversationIds));
            $this->db->delete($this->conversationsTable, "conversation_id IN ($ids)");
        }

        if ($rcsUserIds) {
            $ids = implode(',', array_unique($rcsUserIds));
            $this->db->delete($this->rcsUsersTable, "id IN ($ids)");
        }

        if ($messageIds) {
            $ids = implode(',', array_unique($messageIds));
            $this->db->delete($this->messagesTable, 
                "message_id IN ($ids) AND user_id = '$userId'"
            );
        }
    }
    
    private function saveMessage($userId, $phoneNumber, $campaign, $conversationId, $result) {
        return $this->db->insert($this->messagesTable, [
            'user_id' => $userId,
            'campaign_id' => $campaign['id'] ?? null,
            'conversation_id' => $conversationId ?? null,
            'destination' => $phoneNumber,
            'message_type' => 'text',
            'direction' => 'outgoing',
            'content' =>  $campaign['message'],
            'interaction_type' => ($campaign['type'] == "promotional" && $campaign['response_handling'] == "automated") ? "automated" : "manual",
            'rcs_message_id' => $result->getMessageId(),
            'error' => $result->isSuccess() ? null : $result->getMessage()
        ]);
    }

    private function updatePromptReferences($promptId, $newNextpromptId) {
        $this->db->update($this->conversationPromtsTable,['next_prompt_id' => $newNextpromptId],"next_prompt_id = '{$promptId}'");
    }

    private function mapResponseType($oldType) {
        $typeMap = [
            'text' => 'text',
            'keyword' => 'keyword',
            'options' => 'options',
            // Add more mappings as needed
        ];
        
        return $typeMap[$oldType] ?? 'text';
    }
}