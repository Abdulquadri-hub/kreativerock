<?php

require_once 'SmsIntegration.php';
require_once 'Conversation.php';

class SmsCampaign {
    private $db;
    private $smsIntegration;
    private $conversation;
    private $campaignTable = 'sms_campaigns';
    private $messagesTable = 'messages';
    private $campaignMessagesTable = 'campaign_messages';
    private $keywordResponsesTable = 'keyword_responses';
    private $promptsResponsesTable = 'prompts_responses';
    private $rcsUsersTable = 'rcs_users';
    private $conversationsTable = 'conversations';
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

        
        $phoneNumbers = is_array($params['contacts']) ? $params['contacts'] : [$params['contacts']];
        
        foreach ($phoneNumbers as $number) {
            $rcsCapable = $this->smsIntegration->checkRcsCapability($number);
            // Store RCS capability info if needed
        }
        
        $campaignData = [
            'user_id' => $user['id'],
            'name' => $params['campaignname'],
            'type' => $params['campaigntype'],
            'message' => $params['campaignmessage'],
            'phone_numbers' => json_encode($phoneNumbers),
            'sms_pages' => $params['smspages'],
            'status' => 'draft',
            'scheduled_date' => $params['scheduled'] === 'NOW' ? null : $params['scheduledDate'],
            'repeat_interval' => $params['repeatcampaign'],
            'response_handling' => $params['responseHandling'] ?? 'manual'
        ];
        
        try {
            $this->db->beginTransaction();
            
            $existingCampaign = $this->db->find($this->campaignTable, 
                "user_id = '{$user['id']}' AND status = 'draft'"
            );
            
            if ($existingCampaign) {
                $campaignId = $existingCampaign['id'];
                $this->db->update($this->campaignTable, $campaignData, "id = '$campaignId'");
            } else {
                $campaignId = $this->db->insert($this->campaignTable, $campaignData);
            }

            $this->db->commitTransaction();

            if ($params['scheduled'] === 'NOW') {
                return $this->sendCampaign($campaignId, $email);
            }
            
            return [
                'status' => true,
                'message' => 'Campaign created successfully',
                'campaign_id' => $campaignId
            ];

        } catch (Exception $e) {
            $this->db->rollbackTransaction();
            error_log($e->getMessage());
            return ['status' => false, 'message' => 'Error creating campaign: ' . $e->getMessage()];
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
        
        if (empty($params['campaignname'])) {
            $this->errors['campaignname'] = 'Campaign name is required';
        }
        
        if (empty($params['campaignmessage'])) {
            $this->errors['campaignmessage'] = 'Message is required';
        } elseif (strlen($params['campaignmessage']) > 160) {
            $this->errors['campaignmessage'] = 'Message cannot exceed 160 characters';
        }
        
        if (empty($params['contacts'])) {
            $this->errors['contacts'] = 'Contacts are required';
        }
        
        if (empty($params['campaigntype'])) {
            $this->errors['campaigntype'] = 'Campaign type is required';
        }
        
        if ($params['scheduled'] !== 'NOW' && empty($params['scheduledDate'])) {
            $this->errors['scheduledDate'] = 'Schedule date is required for scheduled campaigns';
        }
        
        return empty($this->errors);
    }
    
    private function sendCampaign($campaignId, $email) {
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
        
        $responses = [];
        foreach ($results as $phoneNumber => $result) {
            $messageId = $this->saveMessage($campaign['user_id'], $phoneNumber, $campaign['message'], $result);
            
            if ($messageId) {
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $campaignId,
                    'message_id' => $messageId,
                    'position' => 'first'
                ]);
            }
            
            $responses[] = [
                'phone' => $phoneNumber,
                'status' => $result->isSuccess() ? 'sent' : 'failed',
                'message_id' => $result->getMessageId(),
                'error' => $result->isSuccess() ? null : $result->getMessage()
            ];
        }
        
        $this->db->update($this->campaignTable, ['status' => 'completed'], "id = '$campaignId'");
        
        return [
            'status' => true,
            'message' => 'Campaign sent successfully',
            'data' => $responses
        ];
    }
    
    private function handlePrompts($campaignId, $userId, $prompts) {
        foreach ($prompts as $prompt) {
            $promptData = [
                'prompt' => $prompt['prompt'],
                'campaign_id' => $campaignId,
                'user_id' => $userId,
                'expected_response' => $prompt['expectedResponse'],
                'response_message' => $prompt['response'],
                'expected_response_type' => $prompt['expectedResponseType'],
                'next_prompt_id' => $prompt['nextPromptId'] ?? null
            ];

            $existingPrompt = $this->db->find(
                $this->promptsResponsesTable, 
                "campaign_id = '$campaignId' AND user_id = '$userId'"
            );
            
            if ($existingPrompt) {
                $this->db->update(
                    $this->promptsResponsesTable, 
                    $promptData, 
                    "id = '{$existingPrompt['id']}'"
                );
            } else {
                $this->db->insert($this->promptsResponsesTable, $promptData);
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
    
    private function saveMessage($userId, $phoneNumber, $message, $result) {
        return $this->db->insert($this->messagesTable, [
            'user_id' => $userId,
            'destinations' => json_encode($phoneNumber),
            'message_type' => 'text',
            'direction' => 'outgoing',
            'content' => $message,
            'interaction_type' => 'prompt',
            'rcs_message_id' => $result->getMessageId(),
            'error' => $result->isSuccess() ? null : $result->getMessage()
        ]);
    }

    public function checkMessageStatus($messageId) {
        return $this->smsIntegration->checkMessageStatus($messageId);
    }

    public function checkRcsCapability($phoneNumber) {
        return $this->smsIntegration->checkRcsCapability($phoneNumber);
    }
}