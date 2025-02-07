<?php
class DBConnection{
    
    // properties
    private $host = 'localhost';
    private $dbname = "db_kreativerock";
    private $dbuser = "root";
    private $dbpass = "";

    // methods
    public function __construct(){}
	public function openConnection(){
        $dbconn = new mysqli($this->host, $this->dbuser, $this->dbpass, $this->dbname);
        return $dbconn;
        		
    }
	public function closeConnection($db){
		mysqli_close($db);
	}


    
    // // From ConversationManager
    // private function getOrStartConversation($contactId) {
    //     $conversation = $this->db->find(
    //         $this->conversationsTable,
    //         "contact_id = ? AND status = 'open'",
    //         [$contactId]
    //     );

    //     if (!$conversation) {
    //         return $this->startConversation(null, $contactId);
    //     }

    //     return $conversation;
    // }

    // // Modified from ConversationManager
    // public function startConversation($campaignId = null, $contactId = null, $reply = null, $currentNodeId = null) {
    //     if ($currentNodeId === null && $campaignId !== null) {
    //         // Get the first node of the campaign
    //         $firstNode = $this->db->find(
    //             $this->conversationNodesTable,
    //             "campaign_id = ? AND parent_node_id IS NULL",
    //             [$campaignId]
    //         );
    //         $currentNodeId = $firstNode['id'] ?? null;
    //     }

    //     $conversationId = $this->db->insert($this->conversationsTable, [
    //         'campaign_id' => $campaignId,
    //         'contact_id' => $contactId,
    //         'user_input' => $reply,
    //         'current_node_id' => $currentNodeId,
    //         'status' => "open"
    //     ]);

    //     // Send initial node message if starting new conversation
    //     if ($currentNodeId) {
    //         $this->sendPromptMessage($conversationId, $currentNodeId);
    //     }

    //     return $this->db->find($this->conversationsTable, "id = ?", [$conversationId]);
    // }

    // // From ConversationManager
    // private function processReply($conversationId, $reply) {
    //     $conversation = $this->db->find(
    //         $this->conversationsTable,
    //         "id = ?",
    //         [$conversationId]
    //     );

    //     if (!$conversation) {
    //         throw new Exception("Conversation not found");
    //     }

    //     // Record the user's message
    //     $this->recordMessage($conversationId, $reply, 'incoming');

    //     // Get current node
    //     $currentNode = $this->db->find(
    //         $this->conversationNodesTable,
    //         "id = ?",
    //         [$conversation['current_node_id']]
    //     );

    //     if (!$currentNode) {
    //         return;
    //     }

    //     // Validate response based on response_type and response_value
    //     if ($this->validateResponse($reply, $currentNode['response_type'], $currentNode['response_value'])) {
    //         $this->transitionToNextNode($conversationId, $currentNode['next_node_id']);
    //     } else {
    //         // Invalid response - resend current node message
    //         $this->sendPromptMessage($conversationId, $conversation['current_node_id']);
    //     }
    // }

    // // From ConversationManager
    // private function transitionToNextNode($conversationId, $nextNodeId) {
    //     if ($nextNodeId) {
    //         $this->db->update(
    //             $this->conversationsTable,
    //             ['current_node_id' => $nextNodeId],
    //             "id = ?",
    //             [$conversationId]
    //         );
            
    //         // Send next node's message
    //         $this->sendPromptMessage($conversationId, $nextNodeId);
    //     } else {
    //         // No next node - close conversation
    //         $this->db->update(
    //             $this->conversationsTable,
    //             [
    //                 'status' => 'closed',
    //                 'current_node_id' => null
    //             ],
    //             "id = ?",
    //             [$conversationId]
    //         );
    //     }
    // }

    // // Modified from ConversationManager
    // private function validateResponse($reply, $responseType, $responseValue) {
    //     switch ($responseType) {
    //         case 'text':
    //             return true;
    //         case 'keyword':
    //             return stripos($responseValue, $reply) !== false;
    //         case 'options':
    //             $options = explode(',', $responseValue);
    //             return in_array(trim(strtolower($reply)), array_map('trim', array_map('strtolower', $options)));
    //         default:
    //             return false;
    //     }
    // }

    // // Combined from both classes
    // private function sendPromptMessage($conversationId, $nodeId) {
    //     $conversation = $this->db->find($this->conversationsTable,"id = '{$conversationId}'");

    //     $node = $this->db->find(
    //         $this->conversationNodesTable,
    //         "id = ?",
    //         [$nodeId]
    //     );

    //     if (!$node || !$conversation) {
    //         return;
    //     }

    //     $messageId = $this->recordMessage(
    //         $conversationId,
    //         $node['message_text'],
    //         'outgoing',
    //         'automated'
    //     );

    //     // Send through SMS integration
    //     $result = $this->smsIntegration->sendBulkOneWaySms(
    //         [$conversation['contact_id']], 
    //         $node['message_text']
    //     );

    //     $this->updateMessageStatus($messageId, $result);
    // }

    // // match
    // private function recordMessage($conversationId, $content, $direction, $interactionType = 'automated') {
    //     $conversation = $this->db->find(
    //         $this->conversationsTable,
    //         "id = ?",
    //         [$conversationId]
    //     );

    //     return $this->db->insert($this->messagesTable, [
    //         'conversation_id' => $conversationId,
    //         'campaign_id' => $conversation['campaign_id'],
    //         'contact_id' => $conversation['contact_id'],
    //         'content' => $content,
    //         'direction' => $direction,
    //         'message_type' => 'text',
    //         'interaction_type' => $interactionType,
    //         'status' => 'pending'
    //     ]);
    // }

    // private function updateMessageStatus($messageId, $result) {
    //     if ($result && $result[0]->isSuccess()) {
    //         $this->db->update(
    //             $this->messagesTable,
    //             [
    //                 'status' => 'sent',
    //                 'rcs_message_id' => $result[0]->getMessageId()
    //             ],
    //             "id = ?",
    //             [$messageId]
    //         );
    //     } else {
    //         $this->db->update(
    //             $this->messagesTable,
    //             [
    //                 'status' => 'failed',
    //                 'error' => $result[0]->getMessage()
    //             ],
    //             "id = ?",
    //             [$messageId]
    //         );
    //     }
    // }
}
?>