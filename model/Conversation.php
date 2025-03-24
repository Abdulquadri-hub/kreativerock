<?php

class Conversation {
    private $db;
    private $smsIntegration;
    private $conversationsTable = 'conversations';
    private $conversationPromptsTable = 'conversation_prompts';
    private $messagesTable = 'messages';
    private $campaignTable = 'campaigns';
    private $logger;

    public function __construct() {
        $this->db = new dbFunctions();
        $this->smsIntegration = new SmsIntegration();
        $this->logger = new Logger(); 
    }

    public function startConversation($campaignId = null, $contactId = null, $reply = null, $currentPromptId = null) {
        // Check for existing open conversation
        $conversation = $this->db->find($this->conversationsTable, "contact_id = '$contactId' AND status = 'open'");
        

        if (!empty($conversation)) {
            
            if ($reply !== null) {
                $this->processReply($conversation['id'], $reply);
            }
            return $conversation['id'];
        } else {
            
            if ($currentPromptId === null && $campaignId !== null) {
                // Get first prompt by sequence order instead of parent_prompt_id
                $firstPrompt = $this->db->find($this->conversationPromptsTable,"campaign_id = '$campaignId' AND sequence_order = 1");
                $currentPromptId = $firstPrompt['id'] ?? null;
            }

            $conversationId = $this->db->insert($this->conversationsTable, [
                'campaign_id' => $campaignId,
                'contact_id' => $contactId,
                'user_input' => $reply,
                'current_prompt_id' => $currentPromptId,
                'status' => "open"
            ]);

            // Send initial message if starting new conversation
            if ($currentPromptId) {
                $this->sendPromptMessage($conversationId, $currentPromptId);
            }

            return $conversationId;
        }
    }


    private function processReply($conversationId, $reply) {
        $conversation = $this->db->find($this->conversationsTable, "id = '{$conversationId}'");

        if (!$conversation) {
            throw new Exception("Conversation not found");
        }

        // $this->recordMessage($conversationId, $reply, 'incoming');
        $messageData = [
            'conversation_id' => $conversationId,
            'campaign_id' => $conversation['campaign_id'],
            'contact_id' => $conversation['contact_id'],
            'content' => $reply,
            'direction' => 'incoming',
            'message_type' => 'text',
            'interaction_type' => 'automated',
            'destination' => $conversation['contact_id'],
            'status' => 'reply',
        ];
        $this->db->insert($this->messagesTable, $messageData);

        $currentPrompt = $this->db->find($this->conversationPromptsTable, "id = '{$conversation['current_prompt_id']}'");

        if (!$currentPrompt) {
            throw new Exception("Conversation prompt not found");
        }

        $validationResult = $this->validateResponse($reply, $currentPrompt);
        
        if ($validationResult['valid']) {
            $nextPrompt = $this->getNextPrompt($conversation['campaign_id'], $currentPrompt['sequence_order']);
  
            if (isset($validationResult['message'])) {
                $this->logger->info("yes: ". $validationResult['message']);
                // $this->recordMessage($conversationId, $validationResult['message'], 'outgoing');
               $results = $this->smsIntegration->sendBulkOneWaySms([$conversation['contact_id']], $validationResult['message']);

                foreach ($results as $phoneNumber => $result) {
                   
                    $messageData = [
                        'conversation_id' => $conversationId,
                        'campaign_id' => $conversation['campaign_id'],
                        'contact_id' => $conversation['contact_id'],
                        'content' => $validationResult['message'],
                        'direction' => 'outgoing',
                        'message_type' => 'text',
                        'interaction_type' => 'automated',
                        'destination' => $phoneNumber,
                        'status' => ($result && $result->isSuccess()) ? 'sent' : 'failed',
                        'error' => ($result && $result->isSuccess()) ? null : ($result ? $result->getMessage() : 'Failed to send')
                    ];
                    
                    // Add message ID if available
                    if ($result && $result->isSuccess()) {
                        $messageData['rcs_message_id'] = $result->getMessageId();
                    }
                    
                    // Insert complete record at once
                   
                    $this->db->insert($this->messagesTable, $messageData);
                    $this->logger->info("yes". $validationResult['message']. "is saved");
                }
                
            }

            if ($nextPrompt) {
                $this->transitionToNextNode($conversationId, $nextPrompt['id']);
            } else {

                if (isset($currentPrompt['is_end_prompt']) && $currentPrompt['is_end_prompt']) {
                    $this->db->update(
                        $this->conversationsTable,
                        ['status' => 'closed', 'current_prompt_id' => null],
                        "id = '$conversationId'"
                    );
                }
            }

        } else {
            // Handle invalid response - resend the current node's message
            $this->sendPromptMessage($conversationId, $conversation['current_prompt_id']);
        }
    }

    private function sendPromptMessage($conversationId, $promptId) {
        $conversation = $this->db->find($this->conversationsTable, "id = '{$conversationId}'");
        $prompt = $this->db->find($this->conversationPromptsTable, "id = '{$promptId}'");

        $campaign = $this->db->find($this->campaignTable, "id = '{$conversation['campaign_id']}'");
        $phoneNumbers = json_decode($campaign['phone_numbers'], true);

        if (!$prompt || !$conversation) {
            return;
        }
        
        // Send through SMS integration
        $results = $this->smsIntegration->sendBulkOneWaySms($phoneNumbers, $prompt['message_text']);
        

        foreach ($results as $phoneNumber => $result) {
            // Prepare all message data at once
            $messageData = [
                'conversation_id' => $conversationId,
                'campaign_id' => $conversation['campaign_id'],
                'contact_id' => $conversation['contact_id'],
                'content' => $prompt['message_text'],
                'direction' => 'outgoing',
                'message_type' => 'text',
                'interaction_type' => 'automated',
                'destination' => $phoneNumber,
                'status' => ($result && $result->isSuccess()) ? 'sent' : 'failed',
                'error' => ($result && $result->isSuccess()) ? null : ($result ? $result->getMessage() : 'Failed to send')
            ];
            
            // Add message ID if available
            if ($result && $result->isSuccess()) {
                $messageData['rcs_message_id'] = $result->getMessageId();
            }
            
            // Insert complete record at once
            $this->db->insert($this->messagesTable, $messageData);
        }
   } 
  

    private function recordMessage($conversationId, $content, $direction, $interactionType = 'automated') {
        $conversation = $this->db->find($this->conversationsTable, "id = '{$conversationId}'");

        return $this->db->insert($this->messagesTable, [
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

    private function validateResponse($reply, $currentPrompt) {
        $responseType = $currentPrompt['response_type'];
        
        // Default response
        $result = ['valid' => false];
        
        switch ($responseType) {
            case 'full_text':
                // Accept any text
                $result = ['valid' => true];
                break;
                
            case 'keyword':
                // Parse the JSON-encoded keyword and response
                $responseValue = json_decode($currentPrompt['response_value'], true);
                
                if (is_array($responseValue) && isset($responseValue['keyword'])) {
                    // Check if the reply contains the keyword (case-insensitive)
                    if (stripos(strtolower($reply), strtolower($responseValue['keyword'])) !== false) {
                        $result = [
                            'valid' => true,
                            'message' => $responseValue['response'] ?? null
                        ];
                    }
                } else {
                    // Fallback to the old method if JSON parsing fails
                    $responseValue = $currentPrompt['response_value'];
                    $result = ['valid' => (stripos($responseValue, $reply) !== false)];
                }
                break;
                
            case 'options':
                $options = explode(',', $currentPrompt['response_value']);
                $result = [
                    'valid' => in_array(
                        trim(strtolower($reply)), 
                        array_map('trim', array_map('strtolower', $options))
                    )
                ];
                break;
        }
        
        return $result;
    }


    private function transitionToNextNode($conversationId, $nextPromptId) {
        if ($nextPromptId) {
            $this->db->update(
                $this->conversationsTable,
                ['current_prompt_id' => $nextPromptId],
                "id = '{$conversationId}'"
            );

            $this->sendPromptMessage($conversationId, $nextPromptId);
        } else {
            // If there's no next prompt, mark conversation as completed
            $this->db->update(
                $this->conversationsTable,
                ['status' => 'closed', 'current_prompt_id' => null],
                "id = '$conversationId'"
            );
        }
    }
    
    /**
     * Get the next prompt in sequence for a campaign
     */
    private function getNextPrompt($campaignId, $currentSequence) {
        return $this->db->find(
            $this->conversationPromptsTable,
            "campaign_id = '$campaignId' AND sequence_order = " . ($currentSequence + 1)
        );
    }

    public function getContacts($campaignId, $status = null) {
        $sql = "SELECT DISTINCT c.contact_id, ct.firstname, ct.lastname,ct.email, ct.sms, ct.whatsapp,
                COUNT(m.id) as message_count, 
                MAX(m.created_at) as last_interaction,
                c.status as conversation_status
                FROM {$this->conversationsTable} c
                JOIN contacts ct ON c.contact_id = ct.id
                LEFT JOIN {$this->messagesTable} m ON c.id = m.conversation_id
                WHERE c.campaign_id = '$campaignId'";
        
        if ($status !== null) {
            $sql .= " AND c.status = '$status'";
        }
        
        $sql .= " GROUP BY c.contact_id, ct.firstname, ct.lastname,ct.email, c.status
                  ORDER BY last_interaction DESC";
        
        return $this->db->query($sql);
    }
    
    /**
     * Get the complete message chain between a sender and recipient for a specific conversation
     */
    public function getMessageChain($conversationId, $contactId = null) {
        $whereClause = "m.conversation_id = '$conversationId'";
        
        if ($contactId !== null) {
            $whereClause .= " AND m.contact_id = '$contactId'";
        }
        
        $sql = "SELECT m.id, m.content, m.direction, m.created_at, m.status,
                c.contact_id, ct.firstname as contact_name, ct.sms as contact_sms, ct.whatsapp as contact_whatsapp,
                m.interaction_type,
                CASE 
                    WHEN m.direction = 'outgoing' THEN u.firstname
                    ELSE NULL
                END as sender_name,
                p.message_text as prompt_text,
                p.response_type,
                p.sequence_order
                FROM {$this->messagesTable} m
                JOIN {$this->conversationsTable} c ON m.conversation_id = c.id
                JOIN contacts ct ON c.contact_id = ct.id
                LEFT JOIN users u ON m.user_id = u.id
                LEFT JOIN {$this->conversationPromptsTable} p ON c.current_prompt_id = p.id
                WHERE $whereClause
                ORDER BY m.created_at ASC";
        
        return $this->db->query($sql);
    }

}