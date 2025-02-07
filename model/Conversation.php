<?php

class Conversation {
    private $db;
    private $smsIntegration;
    private $conversationsTable = 'conversations';
    private $conversationPromptsTable = 'conversation_prompts';
    private $messagesTable = 'messages';

    public function __construct() {
        $this->db = new dbFunctions();
        $this->smsIntegration = new SmsIntegration();
    }

    public function startConversation($campaignId = null, $contactId = null, $reply = null, $currentPromptId = null) {

        $conversation = $this->db->find($this->conversationsTable, "contact_id = '$contactId' AND status = 'open'");

        if (!empty($conversation)) {
            // If there's a reply, process it and move to next node
            if ($reply !== null) {
                $this->processReply($conversation['id'], $reply);
            }
            return $conversation['id'];
        } else {
            // Start new conversation
            if ($currentPromptId === null && $campaignId !== null) {
                $firstPrompt = $this->db->find($this->conversationPromptsTable,"campaign_id = '$campaignId' AND parent_prompt_id IS NULL");
                $currentPromptId = $firstPrompt['id'] ?? null;
            }

            $conversationId = $this->db->insert($this->conversationsTable, [
                'campaign_id' => $campaignId,
                'contact_id' => $contactId,
                'user_input' => $reply,
                'current_prompt_id' => $currentPromptId,
                'status' => "open"
            ]);

            // // Send initial message if starting new conversation
            // if ($currentPromptId) {
            //     $this->sendPromptMessage($conversationId, $currentPromptId);
            // }

            return $conversationId;
        }
    }

    /**
     * Process user reply and handle conversation flow
     */
    private function processReply($conversationId, $reply) {
        $conversation = $this->db->find($this->conversationsTable,"id = '{$conversationId}'");

        if (!$conversation) {
            throw new Exception("Conversation not found");
        }

        // Record the user's message
        $this->recordMessage($conversationId, $reply, 'incoming');

        // Get current node
        $currentPrompt = $this->db->find($this->conversationPromptsTable,"id = '{$conversation['current_prompt_id']}'");

        if (!$currentPrompt) {
            throw new Exception("Conversation prompt not found");
        }

        // Validate response based on response_type and response_value
        if ($this->validateResponse($reply, $currentPrompt['response_type'], $currentPrompt['response_value'])) {
            // Move to next node
            $this->transitionToNextNode($conversationId, $currentPrompt['next_prompt_id']);
            
            // If there's a next node, send its message
            if ($currentPrompt['next_prompt_id']) {
                $this->sendPromptMessage($conversationId, $currentPrompt['next_prompt_id']);
            }
        } else {
            // Handle invalid response - maybe resend the current node's message
            $this->sendPromptMessage($conversationId, $conversation['current_prompt_id']);
        }
    }

    /**
     * Send a prompt's message to the user
     */
    private function sendPromptMessage($conversationId, $promptId) {
        $conversation = $this->db->find($this->conversationsTable,"id = '{$conversationId}'");

        $prompt = $this->db->find($this->conversationPromptsTable, "id = '{$promptId}'");

        if (!$prompt || !$conversation) {
            return;
        }
        
        if ($prompt) {
            $this->recordMessage($conversationId, $prompt['message_text'], 'outgoing');
        }

        $messageId = $this->recordMessage($conversationId,$prompt['message_text'],'outgoing','automated');

        // Send through SMS integration
        $result = $this->smsIntegration->sendBulkOneWaySms([$conversation['contact_id']], $prompt['message_text']);

        // $this->updateMessageStatus($messageId, $result);
    }

    private function updateMessageStatus($messageId, $result) {
        if ($result && $result[0]->isSuccess()) {
            $this->db->update(
                $this->messagesTable,
                [
                    'status' => 'sent',
                    'rcs_message_id' => $result[0]->getMessageId()
                ],
                "id = ?",
                [$messageId]
            );
        } else {
            $this->db->update(
                $this->messagesTable,
                [
                    'status' => 'failed',
                    'error' => $result[0]->getMessage()
                ],
                "id = ?",
                [$messageId]
            );
        }
    }

    /**
     * Record a message in the messages table  
     */
    private function recordMessage($conversationId, $content, $direction, $interactionType = 'automated') {
        $conversation = $this->db->find($this->conversationsTable,"id = '{$conversationId}'");

        $this->db->insert($this->messagesTable, [
            'conversation_id' => $conversationId,
            'campaign_id' => $conversation['campaign_id'],
            'contact_id' => $conversation['contact_id'],
            'content' => $content,
            'direction' => $direction,
            'message_type' => 'text',
            'interaction_type' => $interactionType,
            'status' => 'pending'
        ]);
    }

    /**
     * Validate user response against expected response type and value
     */
    private function validateResponse($reply, $responseType, $responseValue) {
        switch ($responseType) {
            case 'text':
                return true; // Accept any text
            case 'keyword':
                return stripos($responseValue, $reply) !== false;
            case 'options':
                $options = explode(',', $responseValue);
                return in_array(trim(strtolower($reply)), array_map('trim', array_map('strtolower', $options)));
            default:
                return false;
        }
    }

    private function transitionToNextNode($conversationId, $nextPromptId) {
        if ($nextPromptId) {
            $this->db->update($this->conversationsTable,['current_prompt_id' => $nextPromptId],"id = '{$conversationId}'");

            $this->sendPromptMessage($conversationId, $nextPromptId);
        } else {
            // If there's no next prompt, mark conversation as completed
            $this->db->update($this->conversationsTable,[
                    'status' => 'closed',
                    'current_prompt_id' => null
                ],
                "id = '$conversationId'",
            );
        }
    }
}
