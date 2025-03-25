<?php

class TwoWayWhatsApp {
    private $db;
    private $conversation;
    private $messagesTable = 'messages';
    private $templatesTable = 'templates';
    private $conversationsTable = 'conversations';
    private $conversationPromptsTable = 'conversation_prompts';
    private $api;
    private $logger;
    private $sourceName = 'KreativeRock';
    private $sourceNumber = 2349128798369;
    
    public function __construct() {
        $this->db = new dbFunctions();
        $this->api = new GupshupAPI();
        $this->conversation = new Conversation();
        $this->logger = new Logger(); 
    }

    public function handleWebhook($webhookData) {
        try {
            // Validate the webhook data
            if (!isset($webhookData['type'])) {
                $this->logger->error("Invalid webhook data: missing event type");
                return null;
            }
            
            switch ($webhookData['type']) {
                case 'message':
                    return $this->handleIncomingMessage($webhookData);
                case 'message-event':
                    return $this->handleMessageStatus($webhookData);
                case 'template-event':
                    return $this->handleTemplateEvent($webhookData);
                default:
                    $this->logger->warning("Unknown webhook event type: " . $webhookData['type']);
                    return null;
            }
        } catch (Exception $e) {
            $this->logger->error("Error processing WhatsApp webhook: " . $e->getMessage());
            return null;
        }
    }

    public function handleIncomingMessage($webhookData) {
        try {
            // Extract message data from webhook payload
            $payload = $webhookData['payload'] ?? null;
            if (!$payload) {
                $this->logger->error("Missing payload in incoming WhatsApp message");
                return null;
            }

            $phoneNumber = $payload['sender']['phone'] ?? null;
            $messageContent = $payload['payload']['text'] ?? null;
            $messageId = $payload['id'] ?? null;
            
            if (!$phoneNumber || !$messageContent) {
                $this->logger->error("Missing required fields in WhatsApp message payload");
                return null;
            }
            
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
                
                $this->logger->info("Processed WhatsApp reply to conversation: $conversationId");
            } else {
                // Send default response for unrecognized message
                $defaultMessage = "Sorry, we didn't recognize that message. Please try again or text HELP for assistance.";
                $this->sendWhatsAppMessage($phoneNumber, $defaultMessage);
                $this->logger->info("Sent default WhatsApp response to unrecognized message: $phoneNumber");
            }
            
            return $payload;
        } catch (Exception $e) {
            $this->logger->error("Error handling incoming WhatsApp message: " . $e->getMessage());
            throw $e; // Re-throw to allow higher-level handling
        }
    }

    public function handleMessageStatus($webhookData) {
        try {
            $payload = $webhookData['payload'] ?? null;
            if (!$payload) {
                $this->logger->error("Missing payload in WhatsApp message status event");
                return null;
            }

            $messageId = $payload['id'] ?? null;
            $status = $payload['type'] ?? null; // sent, delivered, read, etc.
            $destination = $payload['destination'] ?? null;
            
            if (!$messageId || !$status) {
                $this->logger->error("Missing required fields in WhatsApp message status payload");
                return null;
            }
            
            // Update message status in database
            $updated = $this->updateMessageStatus($messageId, $status);
            
            if ($updated) {
                $this->logger->info("Updated WhatsApp message status: $messageId to $status");
            } else {
                $this->logger->warning("Failed to update WhatsApp message status: $messageId to $status");
            }
            
            // If message failed, we might want to retry or notify admin
            if ($status === 'failed' || $status === 'undelivered') {
                $message = $this->db->find($this->messagesTable, "gush_message_id = '$messageId'");
                if (!empty($message)) {
                    $this->logger->error("WhatsApp message delivery failed: $messageId to {$message['contact_id']}");
                    // Add retry logic here if needed
                }
            }
            
            return $payload;
        } catch (Exception $e) {
            $this->logger->error("Error handling WhatsApp message status: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function handleTemplateEvent($webhookData) {
        try {
            $payload = $webhookData['payload'] ?? null;
            if (!$payload) {
                $this->logger->error("Missing payload in WhatsApp template event");
                return null;
            }

            $templateId = $payload['id'] ?? null;
            $status = $payload['status'] ?? null; // approved, rejected, deleted, disabled
            $elementName = $payload['elementName'] ?? null;
            $languageCode = $payload['languageCode'] ?? null;
            $rejectedReason = $payload['rejectedReason'] ?? null;
            
            // Log template status changes
            $this->logger->info("WhatsApp template $elementName ($templateId) status changed to: $status");
            
            if ($status === 'rejected') {
                $this->logger->warning("WhatsApp template $elementName ($templateId) was rejected: $rejectedReason");
                
                $this->db->update($this->templatesTable, [
                    'status' => strtoupper($status), 
                    'rejected_reason' => $rejectedReason
                ], "template_id = '$templateId'");
                
                // Notify admins about rejected template
                // $this->notifyAdminAboutRejectedTemplate($elementName, $rejectedReason);
            } else if ($status === 'approved') {
                $this->db->update($this->templatesTable, [
                    'status' => strtoupper($status), 
                    'rejected_reason' => null
                ], "template_id = '$templateId'");
            }
            
            return $payload;
        } catch (Exception $e) {
            $this->logger->error("Error handling WhatsApp template event: " . $e->getMessage());
            throw $e;
        }
    }

    private function updateMessageStatus($messageId, $status) {

        $statusMap = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
            'undelivered' => 'failed',
            'enqueued' => 'pending'
        ];
        
        $internalStatus = $statusMap[$status] ?? $status;
        
        return $this->db->update(
            $this->messagesTable, [
                'status' => $internalStatus
            ], "gush_message_id = '$messageId'");
    }
    
    public function sendWhatsAppMessage($phoneNumber, $message, $campaignId = null, $conversationId = null, $templateId = null) {
        try {
            $appId = $this->getAppIdForDestination();
            
            // Prepare message data
            $messageData = [
                'source' => $this->getSourceNumber(),
                'destination' => $phoneNumber,
                'message_type' => 'text',
                'src.name' => $this->getSourceName()
            ];
            
            if ($templateId) {
                $messageData['template'] = [
                    'id' => $templateId,
                    'params' => [
                        [
                            'type' => 'text',
                            'text' => $message
                        ]
                        // Add more params as needed for template
                    ]
                ];
            } else {
                // Direct text message
                $messageData['message'] = [
                    'type' => 'text',
                    'text' => $message
                ];
            }
            
            $response = $this->api->sendMessage($appId, $templateId, $messageData);
            
            if ($conversationId) {
                $messageId = $response['messageId'] ?? null;
                $status = 'pending';
                
                // Extract status from response if available
                if ($messageId) {
                    $status = 'sent';
                } else {
                    $status = 'failed';
                }
                
                $this->db->insert($this->messagesTable, [
                    'conversation_id' => $conversationId,
                    'campaign_id' => $campaignId,
                    'contact_id' => $phoneNumber,
                    'content' => $message,
                    'direction' => 'outgoing',
                    'message_type' => 'whatsapp',
                    'interaction_type' => $templateId ? 'template' : 'manual',
                    'status' => $status,
                    'gush_message_id' => $messageId,
                    'template_id' => $templateId
                ]);
            }
            
            return $response;
        } catch (Exception $e) {
            $this->logger->error("Error sending WhatsApp message: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function sendBulkWhatsAppMessage($phoneNumbers, $message, $campaignId = null, $templateId = null) {
        $results = [];
        
        foreach ($phoneNumbers as $phoneNumber) {
            // Start a new conversation for each contact
            $conversationId = $this->conversation->startConversation($campaignId, $phoneNumber, null, null);
            
            // Send and record the message
            $results[$phoneNumber] = $this->sendWhatsAppMessage($phoneNumber, $message, $campaignId, $conversationId, $templateId);
        }
        
        return $results;
    }
    
    public function sendTemplateMessage($phoneNumber, $templateId, $params = [], $campaignId = null, $conversationId = null) {
        try {
            $appId = $this->getAppIdForDestination();
            
            // Prepare message data
            $messageData = [
                'source' => $this->getSourceNumber(),
                'destination' => $phoneNumber,
                'src.name' => $this->getSourceName(),
                'template' => [
                    'id' => $templateId,
                    'params' => $params
                ]
            ];
            
            // Send message via API
            $response = $this->api->sendMessage($appId, $templateId, $messageData);
            
            // Construct content from template and params for recording
            $contentPreview = "Template: $templateId";
            
            // Only proceed if we have a conversation ID to track this with
            if ($conversationId) {
                $messageId = $response['messageId'] ?? null;
                $status = $messageId ? 'sent' : 'failed';
                
                // Record message in database
                $this->db->insert($this->messagesTable, [
                    'conversation_id' => $conversationId,
                    'campaign_id' => $campaignId,
                    'contact_id' => $phoneNumber,
                    'content' => $contentPreview,
                    'direction' => 'outgoing',
                    'message_type' => 'whatsapp',
                    'interaction_type' => 'template',
                    'status' => $status,
                    'gush_message_id' => $messageId,
                    'template_id' => $templateId,
                    'template_params' => json_encode($params)
                ]);
            }
            
            return $response;
        } catch (Exception $e) {
            $this->logger->error("Error sending WhatsApp template message: " . $e->getMessage());
            throw $e;
        }
    }
     
    private function getSourceNumber() {
        return $this->sourceNumber;
    }

    private function getSourceName() {
        return $this->sourceName;
    }
    
    private function getAppIdForDestination() {
        return $this->api->getCurrentAppId();
    }
}