<?php

class TwoWaySms {
    private $db;
    private $conversation;
    private $messagesTable = 'messages';
    private $conversationsTable = 'conversations';
    private $conversationNodesTable = 'conversation_nodes';
    private $api;
    
    public function __construct() {
        $this->db = new dbFunctions();
        $this->api = new DotgoApi();
        $this->conversation = new Conversation();
    }

    public function handleIncomingMessage($webhookData) {
        try {
            $messageData = $this->api->webhook($webhookData);
            
            if ($messageData['type'] !== 'incomingMessage' || $messageData['messageType'] !== 'text') {
                return;
            }

            $phoneNumber = $messageData['userContact'];
            $messageContent = $messageData['messageContent'];
            $messageId = $messageData['messageId'];

            $sentMessage = $this->db->find($this->messagesTable, "rcs_message_id = '$messageId' AND status != 'failed'");
            if(empty($sentMessage)) {
                error_log("Message sent cannot be found.");
                return;
            }
            
            $this->conversation->startConversation($sentMessage['campaign_id'], $phoneNumber, $messageContent, null);

        } catch (Exception $e) {
            error_log("Error handling incoming message: " . $e->getMessage());
        }
    }

    public function handleMessageStatus($webhookData) {
  
        if (is_string($webhookData)) {
            $webhookData = json_decode($webhookData, true);
        }
    
        if (!is_array($webhookData)) {
            error_log('Invalid webhook data format');
            return false;
        }
    
        // Handle single message or multiple messages
        $messages = isset($webhookData['type']) ? [$webhookData] : $webhookData;
    
        foreach ($messages as $messageData) {
            if ($messageData['type'] == 'messageStatus') {
                $phoneNumber = $messageData['userContact'];
                $status = $messageData['status'];
                $messageId = $messageData['messageId'];
                $timestamp = $messageData['timestamp'];
                
                // Update all matching messages
                $updateResult = $this->db->update(
                    $this->messagesTable, 
                    ['status' => $status, 'updated_at' => $timestamp], 
                    "rcs_message_id = '{$messageId}'"
                );
    
                // Optional: Log the update result
                if ($updateResult === false) {
                    error_log("Failed to update message status for MessageID: {$messageId}");
                }
            }
        }
    
        return true;
    }
    
}