<?php

class TwoWaySms {
    private $db;
    private $conversation;
    private $messagesTable = 'messages';
    private $conversationsTable = 'conversations';
    private $conversationPromptsTable = 'conversation_prompts';
    private $api;
    private $logger;
    
    public function __construct() {
        $this->db = new dbFunctions();
        $this->api = new DotgoApi();
        $this->conversation = new Conversation();
        $this->logger = new Logger(); 
    }

    /**
     * Main webhook handler that routes to appropriate handlers based on event type
     */
    public function handleWebhook($webhookData) {
        try {
            // Validate the webhook data
            if (!isset($webhookData['event'])) {
                $this->logger->error("Invalid webhook data: missing event type");
                return null;
            }
            
            switch ($webhookData['event']) {
                case 'message':
                    return $this->handleIncomingMessage($webhookData);
                case 'messageStatus':
                    return $this->handleMessageStatus($webhookData);
                case 'response':
                    return $this->handleSuggestedResponse($webhookData);
                case 'isTyping':
                    return $this->handleIsTyping($webhookData);
                default:
                    $this->logger->warning("Unknown webhook event type: " . $webhookData['event']);
                    return null;
            }
        } catch (Exception $e) {
            $this->logger->error("Error processing webhook: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle incoming text messages
     */
    public function handleIncomingMessage($webhookData) {
        try {
            $messageData = $this->api->webhook($webhookData);
            
            if ($messageData['type'] !== 'incomingMessage') {
                $this->logger->info("Not an incoming message: " . $messageData['type']);
                return $messageData;
            }

            $phoneNumber = $messageData['userContact'];
            $messageContent = $messageData['messageContent'];
            $messageId = $messageData['messageId'];
            
            // Send read receipt to acknowledge message
            $this->api->sendReadNotification($messageId);
            
            // First, check if this is a response to an existing conversation
            $existingConversation = $this->db->find(
                $this->conversationsTable, 
                "contact_id = '$phoneNumber' AND status = 'open'"
            );
            
            if (!empty($existingConversation)) {
                // Process as a reply to existing conversation
                $conversationId = $this->conversation->startConversation(
                    $existingConversation['campaign_id'],
                    $phoneNumber,
                    $messageContent,
                    $existingConversation['current_prompt_id']
                );
                
                $this->logger->info("Processed reply to conversation: $conversationId");
            } else {
                // // Check if this message matches the start of any campaigns
                // $campaigns = $this->db->select('campaigns', "status = 'active' AND trigger_keyword IS NOT NULL");
                
                // $matchedCampaign = null;
                // foreach ($campaigns as $campaign) {
                //     $keyword = trim(strtolower($campaign['trigger_keyword']));
                //     if (!empty($keyword) && stripos(strtolower($messageContent), $keyword) !== false) {
                //         $matchedCampaign = $campaign;
                //         break;
                //     }
                // }
                
                // if ($matchedCampaign) {
                //     // Start a new conversation with this campaign
                //     $conversationId = $this->conversation->startConversation(
                //         $matchedCampaign['id'],
                //         $phoneNumber,
                //         $messageContent,
                //         null
                //     );
                    
                //     $this->logger->info("Started new conversation: $conversationId for campaign: {$matchedCampaign['id']}");
                // } else {
                    // Send default response for unrecognized message
                    $defaultMessage = "Sorry, we didn't recognize that message. Please try again or text HELP for assistance.";
                    $this->api->sendOneSmsMessage($phoneNumber, $defaultMessage);
                    $this->logger->info("Sent default response to unrecognized message: $phoneNumber");
                // }
            }
            
            return $messageData;
        } catch (Exception $e) {
            $this->logger->error("Error handling incoming message: " . $e->getMessage());
            throw $e; // Re-throw to allow higher-level handling
        }
    }

    /**
     * Handle message status updates (delivered, read, etc.)
     */
    public function handleMessageStatus($webhookData) {
        try {
            $messageData = $this->api->webhook($webhookData);
            
            if ($messageData['type'] !== 'messageStatus') {
                return $messageData;
            }

            $status = $messageData['status'];
            $messageId = $messageData['messageId'];
            
            // Update message status in database
            $updated = $this->updateMessageStatus($messageId, $status);
            
            if ($updated) {
                $this->logger->info("Updated message status: $messageId to $status");
            } else {
                $this->logger->warning("Failed to update message status: $messageId to $status");
            }
            
            // If message failed, we might want to retry or notify admin
            if ($status === 'failed' || $status === 'undelivered') {
                $message = $this->db->find($this->messagesTable, "rcs_message_id = '$messageId'");
                if (!empty($message)) {
                    $this->logger->error("Message delivery failed: $messageId to {$message['contact_id']}");
                    // Add retry logic here if needed
                }
            }
            
            return $messageData;
        } catch (Exception $e) {
            $this->logger->error("Error handling message status: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Handle suggested responses from RCS
     */
    public function handleSuggestedResponse($webhookData) {
        try {
            $messageData = $this->api->webhook($webhookData);
            
            if ($messageData['type'] !== 'suggestedResponse') {
                return $messageData;
            }

            $phoneNumber = $messageData['userContact'];
            $response = $messageData['response'];
            $messageId = $messageData['messageId'];
            
            // Find conversation by the original message
            $message = $this->db->find($this->messagesTable, "rcs_message_id = '$messageId'");
            if (empty($message)) {
                $this->logger->warning("Original message not found for suggested response: $messageId");
                return $messageData;
            }
            
            // Process the suggested response as a regular message
            $conversationId = $this->conversation->startConversation(
                $message['campaign_id'],
                $phoneNumber,
                $response,
                null
            );
            
            $this->logger->info("Processed suggested response for conversation: $conversationId");
            return $messageData;
        } catch (Exception $e) {
            $this->logger->error("Error handling suggested response: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Handle isTyping events
     */
    public function handleIsTyping($webhookData) {
        try {
            $messageData = $this->api->webhook($webhookData);
            
            if ($messageData['type'] !== 'isTyping') {
                return $messageData;
            }

            $phoneNumber = $messageData['userContact'];
            $isTyping = $messageData['isTyping'];
            
            // Log typing indicators or use them for analytics
            $this->logger->info("User $phoneNumber is typing: $isTyping");
            
            return $messageData;
        } catch (Exception $e) {
            $this->logger->error("Error handling typing indicator: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update message status in the database
     */
    private function updateMessageStatus($messageId, $status) {
        // Map RCS statuses to our internal status values if needed
        $statusMap = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'displayed' => 'read',
            'failed' => 'failed',
            'undelivered' => 'failed',
            'pending' => 'pending'
        ];
        
        $internalStatus = $statusMap[$status] ?? $status;
        
        return $this->db->update(
            $this->messagesTable, 
            ['status' => $internalStatus], 
            "rcs_message_id = ?", 
            [$messageId]
        );
    }
    
    /**
     * Send a message to a contact and record it in the database
     */
    public function sendMessage($phoneNumber, $message, $campaignId = null, $conversationId = null) {
        try {
            // Send message via API
            $response = $this->api->sendOneSmsMessage($phoneNumber, $message);
            
            // Only proceed if we have a conversation ID to track this with
            if ($conversationId) {
                $messageId = null;
                $status = 'pending';
                
                // Extract message ID and status from response if available
                if ($response && method_exists($response, 'isSuccess') && $response->isSuccess()) {
                    $messageId = $response->getMessageId();
                    $status = 'sent';
                } else {
                    $status = 'failed';
                }
                
                // Record message in database
                $this->db->insert($this->messagesTable, [
                    'conversation_id' => $conversationId,
                    'campaign_id' => $campaignId,
                    'contact_id' => $phoneNumber,
                    'content' => $message,
                    'direction' => 'outgoing',
                    'message_type' => 'text',
                    'interaction_type' => 'manual', // or 'automated'
                    'status' => $status,
                    'rcs_message_id' => $messageId
                ]);
            }
            
            return $response;
        } catch (Exception $e) {
            $this->logger->error("Error sending message: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Send a bulk message to multiple contacts
     */
    public function sendBulkMessage($phoneNumbers, $message, $campaignId = null) {
        $results = [];
        
        foreach ($phoneNumbers as $phoneNumber) {
            // Start a new conversation for each contact
            $conversationId = $this->conversation->startConversation($campaignId, $phoneNumber, null, null);
            
            // Send and record the message
            $results[$phoneNumber] = $this->sendMessage($phoneNumber, $message, $campaignId, $conversationId);
        }
        
        return $results;
    }
    
    /**
     * Check RCS capability for a phone number
     */
    public function checkRcsCapability($phoneNumber) {
        try {
            $response = $this->api->checkRCSCapability($phoneNumber);
            
            if ($response && method_exists($response, 'isSuccess') && $response->isSuccess()) {
                $data = $response->getData();
                return isset($data['capabilities']) && in_array('TEXT', $data['capabilities']);
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Error checking RCS capability: " . $e->getMessage());
            return false;
        }
    }

}