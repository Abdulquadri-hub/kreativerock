<?php

require_once 'SmsIntegration.php';

class TwoWaySms {
    private $model;
    private $smsIntegration;
    private $db;
    private $messagesTable = 'messages';
    private $keywordResponsesTable = 'keyword_responses';
    private $promptsResponsesTable = 'prompts_responses';
    private $conversationStateTable = 'conversation_state';
    private $campaignMessagesTable = 'campaign_messages';
    private $usersTable = 'users';
    public $errors = [];
    
    public function __construct() {
        $this->model = new Model();
        $this->db = new dbFunctions();
        $this->api = new DotgoApi();
        $this->smsIntegration = new SmsIntegration();
    }
    
    public function sendReply($data) {
        try {
            $requiredFields = ['campaignid', 'phonenumber', 'reply'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            $email = $data['email'];
            $user = $this->db->find($this->usersTable, "email = '$email'");
            if (!$user) {
                error_log("User not found");
                return;
            }
            
            $conversationDetails = $this->getConversationDetails($data['campaignid'], $data['phonenumber'], $user['id']);

            $messageData = [
                'user_id' => $user['id'],
                'conversation_id' => $conversationDetails[0]['conversation_id'],
                'rcs_user_id' => $conversationDetails[0]['rcs_user_id'],
                'destinations' => $data['phonenumber'],
                'type' => 'text',
                'direction' => 'outgoing',
                'content' => $data['reply'],
                'rcs_message_id' => null,
                'error' => null,
                'interaction_type' => null
            ];

            $messageId = $this->sendMessage(
                $messageData['user_id'],
                $messageData['conversation_id'],
                $messageData['rcs_user_id'],
                $messageData['destinations'],
                $messageData['type'],
                $messageData['direction'],
                $messageData['content'],
                $messageData['rcs_message_id'],
                $messageData['error'],
                $messageData['interaction_type']
            );

            if (!$messageId) {
                error_log("Message reply not sent.");
                return;
            }
            
            $this->smsIntegration->logMessage($messageId, 'message_reply_sent', [
                'campaign_id' => $data['campaignid'],
                'reply' => $data['reply'],
                'phone_number' => $data['phonenumber']
            ]);
            
            if ($messageId) {
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $data['campaignid'],
                    'message_id' => $messageId,
                    'position' => 'others',
                ]);
            }

            return [
                'status' => true,
                'message' => 'Reply sent successfully',
                'data' => $result
            ];

        } catch (Exception $e) {
            error_log("Error sending reply: " . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function handleIncomingMessage($webhookData) {
        $messageData = $this->api->webhook($webhookData);
        
        if ($messageData['type'] == 'incomingMessage' && $messageData['messageType'] == 'text') {
            $messageType = $messageData['messageType'];
            
            $phoneNumber = $messageData['userContact'];
            $messageContent = $messageData['messageContent'];
            $messageId = $messageData['messageId'];
            
            $userId = $this->smsIntegration->getOrCreateRcsUser($phoneNumber);
            $conversationId = $this->smsIntegration->getOrCreateConversation($userId);
            
            $sentMessage = $this->db->select($this->messagesTable, '*', "rcs_message_id = '$messageId' AND status != 'failed'");
            if(empty($sentMessage)) {
                error_log("Message sent cannot be found.");
                return;
            }
            
            $sentMessage = $sentMessage[0];
                    
            $incomingMessageId = $this->create(
                $sentMessage['user_id'],
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'incoming', 
                $messageContent, 
                $messageId, 
                null, 
                $sentMessage['interaction_type']
            );
            
            $firstMessageId = $sentMessage['message_id'];
            $isCampaignMessage = $this->db->select($this->campaignMessagesTable, "*", "message_id = '$firstMessageId'");

            if(!empty($isCampaignMessage) && count($isCampaignMessage) > 0){
                $isCampaignMessage = $isCampaignMessage[0];
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $isCampaignMessage['campaign_id'],
                    'message_id' => $incomingMessageId,
                    'position' => 'others',
                ]);
            }else{
                error_log("First message can not be found.");
                return; 
            }
            
            $this->db->update($this->messagesTable, ['status' => 'recieved'], "message_id = '$incomingMessageId'");
            
            if ($sentMessage['interaction_type'] === 'keyword') {
                $this->handleKeywordResponse($sentMessage['user_id'], $conversationId, $userId, $phoneNumber, $messageContent);
            } else {
                $this->handlePromptsResponse_N($sentMessage['user_id'], $conversationId, $userId, $phoneNumber, $messageContent, $isCampaignMessage);
                
                // $state = $this->getConversationState($conversationId);
                // if ($state) {
                //     $this->handlePromptResponse($sentMessage['user_id'], $conversationId, $userId, [$phoneNumber], $messageContent, $state);
                // } else {
                //     $this->startNewConversation($sentMessage['user_id'], $conversationId, $userId, [$phoneNumber]);
                // }
            }
            
        }elseif($messageData['type'] == 'incomingMessage' && $messageData['messageType'] == 'audio'){
            //
        }
    }

    private function getConversationDetails($campaignId, $phoneNumber, $userId) {
        $query = "
            WITH CampaignConversation AS (
                SELECT 
                    m.message_id,
                    m.content,
                    m.direction,
                    m.status AS message_status,
                    m.created_at,
                    m.destinations,
                    r.phone_number,
                    r.id AS rcs_user_id,
                    c.status AS conversation_status,
                    c.conversation_id,
                    ROW_NUMBER() OVER (PARTITION BY r.phone_number, cm.campaign_id ORDER BY m.created_at DESC) as rn
                FROM campaign_messages cm
                JOIN messages m ON cm.message_id = m.message_id
                LEFT JOIN conversations c ON m.conversation_id = c.conversation_id
                LEFT JOIN rcs_users r ON m.destinations = r.phone_number
                WHERE 
                    cm.campaign_id = ? 
                    AND r.phone_number = ?
                    AND m.user_id = ?
                    AND m.status != 'failed'
            )
            SELECT * FROM CampaignConversation WHERE rn = 1";

        try {
            return $this->db->query($query, [$campaignId, $phoneNumber, $userId]);
        } catch (Exception $e) {
            error_log("Error fetching conversation details: " . $e->getMessage());
            return null;
        }
    }
    
    private function sendMessage($senderId, $conversationId, $userId, $destinations, $type, $direction, $content, $rcsMessageId = null, $error = null, $interactionType = null) {
        $messageId = $this->create(
            $senderId, 
            $conversationId, 
            $userId,
            $destinations, 
            $type, 
            $direction, 
            $content, 
            $rcsMessageId, 
            $error, 
            $interactionType
        );
        return $this->execute($messageId);
    }

    private function handleKeywordResponse($senderId, $conversationId, $userId, $phoneNumber, $message, $isCampaignMessage) {
        $keywordResponse = $this->findMatchingKeyword($message, $isCampaignMessage['campaign_id']);
        if ($keywordResponse) {
            $outgoingMessage  = $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                $keywordResponse['response_message'], 
                null, 
                null, 
                'keyword'
            );
            
            $this->smsIntegration->logMessage($outgoingMessage, 'message_prompts_response_sent', [
                'sender_id' => $senderId,
                'direction' => 'outgoing',
                'campaign_id' => $isCampaignMessage['campaign_id'],
                'response' => $keywordResponse['response_message'],
                'phone_number' => $phoneNumber
            ]);
            
            if(!empty($isCampaignMessage) && count($isCampaignMessage) > 0){
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $isCampaignMessage['campaign_id'],
                    'message_id' => $outgoingMessage,
                    'position' => 'others',
                ]);
            }else{
                error_log("First message can not be found.");
                return; 
            }
        } else {
            $outgoingMessage = $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                "I'm sorry, I didn't understand that keyword. Please try again.", 
                null, 
                null, 
                'keyword'
            );
            
            $this->smsIntegration->logMessage($outgoingMessage, 'message_prompts_response_sent', [
                'sender_id' => $senderId,
                'direction' => 'outgoing',
                'campaign_id' => $isCampaignMessage['campaign_id'],
                'response' => "I'm sorry, I didn't understand that keyword. Please try again.",
                'phone_number' => $phoneNumber
            ]);
            
            if(!empty($isCampaignMessage) && count($isCampaignMessage) > 0){
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $isCampaignMessage['campaign_id'],
                    'message_id' => $outgoingMessage,
                    'position' => 'others',
                ]);
            }else{
                error_log("First message can not be found.");
                return; 
            }            
        }
    }
    
    private function handlePromptsResponse_N($senderId, $conversationId, $userId, $phoneNumber, $message, $isCampaignMessage) {
        $currentPrompt = $this->findMatchingPromptsResponses($message, $isCampaignMessage['campaign_id']);
        if ($currentPrompt) {
            $outgoingMessage = $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                $currentPrompt['response_message'],
                null, 
                null, 
                'prompt'
            );
            
            $this->smsIntegration->logMessage($outgoingMessage, 'message_prompts_response_sent', [
                'sender_id' => $senderId,
                'direction' => 'outgoing',
                'campaign_id' => $isCampaignMessage['campaign_id'],
                'response' => $currentPrompt['response_message'],
                'phone_number' => $phoneNumber
            ]);

            if(!empty($isCampaignMessage) && count($isCampaignMessage) > 0){
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $isCampaignMessage['campaign_id'],
                    'message_id' => $outgoingMessage,
                    'position' => 'others',
                ]);
            }else{
                error_log("First message can not be found.");
                return; 
            }
        } else {
            $outgoingMessage = $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                "I'm sorry, I didn't understand that. Please try again.", 
                null, 
                null, 
                'prompt'
            );
            
            $this->smsIntegration->logMessage($outgoingMessage, 'message_prompts_response_sent', [
                'sender_id' => $senderId,
                'direction' => 'outgoing',
                'campaign_id' => $isCampaignMessage['campaign_id'],
                'response' => $currentPrompt['response_message'],
                'phone_number' => $phoneNumber
            ]);

            if(!empty($isCampaignMessage) && count($isCampaignMessage) > 0){
                $this->db->insert($this->campaignMessagesTable, [
                    'campaign_id' => $isCampaignMessage['campaign_id'],
                    'message_id' => $outgoingMessage,
                    'position' => 'others',
                ]);
            }else{
                error_log("First message can not be found.");
                return; 
            }
        }
    }

    private function findMatchingKeyword($message, $campaignId) {
        $keywords = $this->db->select($this->keywordResponsesTable, '*', "campaign_id = '$campaignId'");
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword['prompt']) !== false) {
                return $keyword;
            }
        }
        return null;
    }
    
    private function findMatchingPromptsResponses($message, $campaignId) {
        $currentPrompts = $this->db->select($this->promptsResponsesTable, '*', "campaign_id = '$campaignId'");
        foreach ($currentPrompts as $currentPrompt) {
            if (strtolower($message) === strtolower($currentPrompt['expected_response'])) {
                return $currentPrompt;
            }
        }
        return null;
    }

    private function handlePromptResponse($senderId, $conversationId, $userId, $phoneNumber, $message, $currentPromptId) {
        $currentPrompt = $this->db->find($this->promptsResponsesTable, "id = '$currentPromptId'");
        if (!$currentPrompt) {
            return;
        }

        if (strtolower($message) === strtolower($currentPrompt['expected_response'])) {
            // Send success response
            $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                $currentPrompt['response_message'], 
                null, 
                null, 
                'prompt'
            );

            // Handle next prompt if exists
            if ($currentPrompt['next_prompt_id']) {
                $this->updateConversationState($conversationId, $currentPrompt['next_prompt_id']);
                $nextPrompt = $this->db->find($this->promptsResponsesTable, "id = '{$currentPrompt['next_prompt_id']}'");
                if ($nextPrompt) {
                    $this->sendMessage(
                        $senderId,
                        $conversationId, 
                        $userId, 
                        $phoneNumber, 
                        'text', 
                        'outgoing', 
                        $nextPrompt['prompt'], 
                        null, 
                        null, 
                        'prompt'
                    );
                }
            } else {
                $this->updateConversationState($conversationId, null);
            }
        } else {
            $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                "I'm sorry, I didn't understand that. Please try again.", 
                null, 
                null, 
                'prompt'
            );
        }
    }

    private function startNewConversation($senderId, $conversationId, $userId, $phoneNumber) {
        $firstPrompt = $this->db->select(
            $this->promptsResponsesTable, 
            '*', 
            "next_prompt_id IS NOT NULL", 
            'id ASC', 
            1
        );
        
        if ($firstPrompt) {
            $this->updateConversationState($conversationId, $firstPrompt[0]['id']);
            $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                $firstPrompt[0]['prompt'], 
                null, 
                null, 
                'prompt'
            );
        } else {
            $this->sendMessage(
                $senderId,
                $conversationId, 
                $userId, 
                $phoneNumber, 
                'text', 
                'outgoing', 
                "Welcome! How can I assist you today?", 
                null, 
                null, 
                'prompt'
            );
        }
    }

    private function create($senderId, $conversationId, $userId, $destinations, $type, $direction, $content, $rcsMessageId = null, $error = null, $interactionType = null) {
        return $this->db->insert($this->messagesTable, [
            'user_id' => $senderId,
            'conversation_id' => $conversationId,
            'rcs_user_id' => $userId,
            'message_type' => $type,
            'destinations' => json_encode($destinations),
            'direction' => $direction,
            'content' => $content,
            'interaction_type' => $interactionType ?? 'prompt',
            'rcs_message_id' => $rcsMessageId,
            'error' => $error ?? null
        ]);
    }

    private function execute($messageId) {
        $message = $this->db->find($this->messagesTable, "message_id = '$messageId'");
        $targetAudience = json_decode($message['destinations'], true);

        $results = $this->smsIntegration->sendBulkOneWaySms([$targetAudience], $message['content']);

        $responses = [];
        foreach ($results as $phoneNumber => $result) {
            $decodedResponse = $result->getDecodedResponse();
            if ($decodedResponse) {
                $responses[] = [
                    'status' => true,
                    'message' => "Message sent successfully.",
                    'rcs' => [
                        'status' => $decodedResponse['data']['RCSMessage']['status'] ?? null,
                        'messageId' => $decodedResponse['data']['RCSMessage']['msgId'] ?? null,
                        'code' => $decodedResponse['code'] ?? null
                    ]
                ];
            }
        }
        
        if (empty($responses)) {
            error_log("No responses received from 2way SMS integration.");
            return false;
        }

        $msgId = $responses[0]['rcs']['messageId'] ?? null;
        if ($msgId) {
            $this->db->update($this->messagesTable, ['rcs_message_id' => $msgId], "message_id = '$messageId'");
        }
        
        return $messageId;
    }

    private function getConversationState($conversationId) {
        $state = $this->db->find($this->conversationStateTable, "conversation_id = '$conversationId'");
        return $state ? $state['current_prompt_id'] : null;
    }

    private function updateConversationState($conversationId, $promptId) {
        $existingState = $this->db->find($this->conversationStateTable, "conversation_id = '$conversationId'");
        if ($existingState) {
            $this->db->update(
                $this->conversationStateTable, 
                ['current_prompt_id' => $promptId], 
                "conversation_id = '$conversationId'"
            );
        } else {
            $this->db->insert($this->conversationStateTable, [
                'conversation_id' => $conversationId,
                'current_prompt_id' => $promptId
            ]);
        }
    }
}