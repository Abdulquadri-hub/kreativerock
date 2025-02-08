<?php

require_once 'WhatsAppIntegration.php';
require_once 'SmsIntegration.php';
require_once 'Conversation.php';

class Campaign {
    private $db;
    private $smsIntegration;
    private $whatsappIntegration;
    private $conversation;
    private $campaignTable = 'campaigns';
    private $messagesTable = 'messages';
    private $contactsTable = 'contacts';
    private $conversationsTable = 'conversations';
    private $conversationPromtsTable = 'conversation_prompts';
    private $usersTable = 'users';
    public $errors = [];
    
    public function __construct() {
        $this->db = new dbFunctions();
        $this->smsIntegration = new SmsIntegration();
        $this->whatsappIntegration = new WhatsAppIntegration();
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

        $segmentIds = is_array($params['segment_id']) ? $params['segment_id'] : [$params['segment_id']];

        $phoneNumbers = [];
        $segmentIds = array_map(function($id) { 
            return $id; 
        }, $segmentIds);
        
        $whereClause = "segment_id IN (" . implode(',', $segmentIds) . ")";
        $contacts = $this->db->select($this->contactsTable, "*", $whereClause);
        
        if(!empty($contacts)){
            foreach ($contacts as $key => $contact) {
                if($params['channel'] === "sms"){
                    $phoneNumbers[] = $contact['sms'];
                }
                elseif($params['channel'] === "whatsapp"){
                    $phoneNumbers[] = $contact['whatsapp'];
                }
            }
        }
        else {
            return [
                'status' => false,
                'code' => 400,
                'message' => 'No contacts found fo this segment',
            ];
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
            'response_handling' => $params['responsehandling'] ?? 'manual',
            'message_type' => $params['message_type'] ?? 'text', // text, media, interactive
            'media_url' => $params['media_url'] ?? null,
            'media_caption' => $params['media_caption'] ?? null,
            'interactive_data' => isset($params['interactive_data']) ? json_encode($params['interactive_data']) : null
        ];
        
        $existingCampaign = $this->db->find($this->campaignTable, "user_id = '{$user['id']}' AND name = '{$params['campaignname']}' AND status = 'draft'");

        if ($existingCampaign) {
            $campaignId = $existingCampaign['id'];
            $this->db->update($this->campaignTable, $campaignData, "id = '$campaignId'");
            return [
                'status' => true,
                'message' => 'Campaign is saved as draft and not sent',
                'campaign_id' => $campaignId,
            ];
        } else {
            $campaignId = $this->db->insert($this->campaignTable, $campaignData);

            if($params['campaigntype'] ===  "promotional" && $params['responsehandling'] === "automated"){
                // $this->handlePrompts($campaignId, $user['id'], $params['prompts']);
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

    public function updateCampaign($params, $email) {

        $user = $this->db->find($this->usersTable, "email = '$email'");
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found'
            ];
        }

        $campaignId = $params['campaign_id'] ?? null;
    
        $existingCampaign = $this->db->find( $this->campaignTable,  "id = '$campaignId' AND user_id = '{$user['id']}'");
        if (!$existingCampaign) {
            return [
                'status' => false,
                'message' => 'Campaign not found'
            ];
        }
    
        if (!$this->validateParams($params)) {
            return [
                'status' => false,
                'message' => 'Missing required fields',
                'errors' => $this->errors
            ];
        }
    
        $phoneNumbers = [];
        $contacts = $this->db->select($this->contactsTable, "*", "segment_id = {$params['segment_id']}");
    
        if (!empty($contacts)) {
            foreach ($contacts as $contact) {
                if ($params['channel'] === "sms") {
                    if ($contact['type'] == "TEXT" || $contact['FILE']) {
                        $phoneNumbers[] = $contact['sms'];
                    } else {
                        $phoneNumbers[] = $contact['sms'];
                    }
                } else {
                    if ($contact['type'] == "TEXT" || $contact['FILE']) {
                        $phoneNumbers[] = $contact['whatsapp'];
                    } else {
                        $phoneNumbers[] = $contact['whatsapp'];
                    }
                }
            }
        } else {
            return [
                'status' => false,
                'code' => 400,
                'message' => 'No contacts found for this segment',
            ];
        }
    
        $updatedCampaignData = [
            'channel' => $params['channel'],
            'name' => $params['campaignname'],
            'type' => $params['campaigntype'],
            'message' => $params['campaignmessage'],
            'phone_numbers' => json_encode($phoneNumbers),
            'sms_pages' => $params['smspages'],
            'scheduled_date' => $params['scheduled'] === 'NOW' ? null : $params['scheduledDate'],
            'repeat_interval' => $params['repeatcampaign'],
            'response_handling' => $params['responsehandling'] ?? 'manual'
        ];
    
        $updated = $this->db->update($this->campaignTable, $updatedCampaignData, "id = '$campaignId'");
    
        if ($updated) {
            if ($params['campaigntype'] === "promotional" && $params['responsehandling'] === "automated") {
                // $this->handlePrompts($campaignId, $user['id'], $params['prompts']);
            }
    
            // if ($params['scheduled'] === 'NOW') {
            //     return $this->sendNow($campaignId, $email);
            // }
    
            return [
                'status' => true,
                'message' => 'Campaign updated successfully',
                'campaign_id' => $campaignId
            ];
        }
    
        return [
            'status' => false,
            'message' => 'Error updating campaign',
            'campaign_id' => $campaignId
        ];
    }
    
    public function deleteCampaign($campaignId, $email) {
        try {
            
            $user = $this->db->find($this->usersTable, "email = '$email'");
            if (!$user) {
                return ['status' => false, 'message' => 'User not found'];
            }
            
            $campaign = $this->db->find($this->campaignTable, "id = '{$campaignId}' AND user_id = '{$user['id']}'");
            if (!$campaign) {
                return ['status' => false, 'message' => 'Campaign not found'];
            }
            
    
            $conversations = $this->db->select($this->conversationsTable, '*', "campaign_id = '{$campaignId}'");
           
            foreach ($conversations as $conversation) {
                $this->db->delete($this->messagesTable, "conversation_id = '{$conversation['id']}'");
            }
            
            $this->db->delete($this->conversationsTable, "campaign_id = '{$campaignId}'");
            
            $this->db->delete($this->conversationPromtsTable, "campaign_id = '{$campaignId}'");
            
            // Delete the campaign itself
            $this->db->delete($this->campaignTable, "id = '{$campaignId}' AND user_id = '{$user['id']}'");
    

            return ['status' => true, 'message' => 'Campaign deleted successfully'];
            
        } catch (Exception $e) {

            return ['status' => false, 'message' => 'Error deleting campaign: ' . $e->getMessage()];
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
                        $this->conversationPromtsTable, 
                        "*", 
                        "campaign_id = '{$campaign['id']}'"
                    );
                }
            }
        }

        return $campaigns ?: null;
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

        if ($params['channel'] === 'sms' && strlen($params['campaignmessage']) > 160) {
            $this->errors['campaignmessage'] = 'SMS message cannot exceed 160 characters';
        }

        if ($params['channel'] === 'whatsapp') {
            if (isset($params['message_type'])) {
                switch ($params['message_type']) {
                    case 'media':
                        if (empty($params['media_url'])) {
                            $this->errors['media_url'] = 'Media URL is required for media messages';
                        }
                        break;
                    case 'interactive':
                        if (empty($params['interactive_data'])) {
                            $this->errors['interactive_data'] = 'Interactive data is required for interactive messages';
                        }
                        break;
                }
            }
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

        if ($campaign['channel'] === 'whatsapp') {
            return $this->whatsAppCampaign($campaign, $phoneNumbers);
        } else {
            return $this->sMsCampaign($campaign, $phoneNumbers);
        }
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

    private function sMsCampaign($campaign, $phoneNumbers) {
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
        
        $this->db->update($this->campaignTable, ['status' => 'completed'], "id = '{$campaign['id']}'");
        
        return [
            'status' => true,
            'message' => 'Campaign sent successfully',
            'data' => $responses
        ];
    }

    private function whatsAppCampaign($campaign, $phoneNumbers) {
        $responses = [];
        $conversationId = null;

        if ($campaign['type'] === "promotional" && $campaign['response_handling'] === "automated") {
            $conversationId = $this->conversation->startConversation($campaign['id']);
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $result = false;

            switch ($campaign['message_type']) {
                case 'text':
                    $result = $this->whatsappIntegration->sendText(
                        $phoneNumber,
                        $campaign['message']
                    );
                    break;

                case 'media':
                    $result = $this->whatsappIntegration->sendMedia(
                        $phoneNumber,
                        $campaign['media_type'],
                        $campaign['media_url'],
                        $campaign['media_caption']
                    );
                    break;

                case 'interactive':
                    $interactiveData = json_decode($campaign['interactive_data'], true);
                    $result = $this->whatsappIntegration->sendInteractive(
                        $phoneNumber,
                        $campaign['message'],
                        $interactiveData['type'],
                        $interactiveData['action']
                    );
                    break;
            }

            $this->saveMessage($campaign['user_id'],$phoneNumber,$campaign,$conversationId,
                (object)[
                    'isSuccess' => function() use ($result) { return $result; },
                    'getMessageId' => function() { return uniqid('wa_'); },
                    'getMessage' => function() { return null; }
                ]
            );

            $responses[] = [
                'phone' => $phoneNumber,
                'status' => $result ? 'sent' : 'failed',
                'campaign_id' => $campaign['id']
            ];
        }

        $this->db->update($this->campaignTable, ['status' => 'completed'], "id = '{$campaign['id']}'");

        return [
            'status' => true,
            'message' => 'WhatsApp campaign sent successfully',
            'data' => $responses
        ];
    }
}