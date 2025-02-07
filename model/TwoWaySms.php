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

    public function handleMessageStatus($webhookData){
        $messageData = $this->api->webhook($webhookData);
        error_log(json_encode($messageData));
        
        if ($messageData['type'] == 'messageStatus') 
        {
            $phoneNumber = $messageData['userContact'];
            $status = $messageData['status'];
            $messageId = $messageData['messageId'];
            $timestamp = $messageData['timestamp'];
            
            $this->updateMessagesStatus($messageId, $status);
        }
    }

    private function updateMessagesStatus($messageId, $status){
        return $this->db->update($this->messagesTable, ['status' => $status], "rcs_message_id = '$messageId'");
    }
}