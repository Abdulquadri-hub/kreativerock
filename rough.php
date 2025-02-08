<?php

require_once 'WhatsAppApi.php';
require_once 'SmsIntegration.php';
require_once 'Conversation.php';

class Campaign {
    private $db;
    private $smsIntegration;
    private $whatsappApi;
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
        $this->whatsappApi = new WhatsAppApi('your_user_id', 'your_password');
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

        $phoneNumbers = [];
        $contacts = $this->db->select($this->contactsTable, "*", "segment_id = {$params['segment_id']}");
        
        if (empty($contacts)) {
            return [
                'status' => false,
                'code' => 400,
                'message' => 'No contacts found for this segment',
            ];
        }

        // Handle different channel types
        foreach ($contacts as $contact) {
            if ($params['channel'] === "sms") {
                $phoneNumbers[] = $contact['sms'];
            } else if ($params['channel'] === "whatsapp") {
                $phoneNumbers[] = $contact['whatsapp'];
            }
        }

        $campaignData = [
            'user_id' => $user['id'],
            "channel" => $params['channel'],
            'name' => $params['campaignname'],
            'type' => $params['campaigntype'],
            'message' => $params['campaignmessage'],
            'phone_numbers' => json_encode($phoneNumbers),
            'status' => 'draft',
            'scheduled_date' => $params['scheduled'] === 'NOW' ? null : $params['scheduledDate'],
            'repeat_interval' => $params['repeatcampaign'],
            'response_handling' => $params['responsehandling'] ?? 'manual',
            // Add WhatsApp specific fields
            'message_type' => $params['message_type'] ?? 'text', // text, media, interactive
            'media_url' => $params['media_url'] ?? null,
            'media_caption' => $params['media_caption'] ?? null,
            'interactive_data' => isset($params['interactive_data']) ? json_encode($params['interactive_data']) : null
        ];

        $existingCampaign = $this->db->find($this->campaignTable, "user_id = '{$user['id']}' AND status = 'draft'");
        
        if ($existingCampaign) {
            $campaignId = $existingCampaign['id'];
            $this->db->update($this->campaignTable, $campaignData, "id = '$campaignId'");
            return [
                'status' => true,
                'message' => 'Campaign updated successfully',
                'campaign_id' => $campaignId,
            ];
        } else {
            $campaignId = $this->db->insert($this->campaignTable, $campaignData);

            if ($params['campaigntype'] === "promotional" && $params['responsehandling'] === "automated") {
                $this->handlePrompts($campaignId, $user['id'], $params['prompts']);
            }

            if ($campaignId) {
                if ($params['scheduled'] === 'NOW') {
                    return $this->sendNow($campaignId, $email);
                }

                return [
                    'status' => true,
                    'message' => 'Campaign created successfully',
                    'campaign_id' => $campaignId,
                ];
            }
        }
    }

    private function sendNow($campaignId, $email) {
        $campaign = $this->db->find($this->campaignTable, "id = '$campaignId'");
        if (!$campaign) {
            return ['status' => false, 'message' => 'Campaign not found'];
        }

        $phoneNumbers = json_decode($campaign['phone_numbers'], true);
        $requiredUnits = count($phoneNumbers);

        // Handle different channels
        if ($campaign['channel'] === 'whatsapp') {
            return $this->sendWhatsAppCampaign($campaign, $phoneNumbers);
        } else {
            return $this->sendSmsCampaign($campaign, $phoneNumbers, $email);
        }
    }

    private function sendWhatsAppCampaign($campaign, $phoneNumbers) {
        $responses = [];
        $conversationId = null;

        if ($campaign['type'] === "promotional" && $campaign['response_handling'] === "automated") {
            $conversationId = $this->conversation->startConversation($campaign['id']);
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $result = false;

            switch ($campaign['message_type']) {
                case 'text':
                    $result = $this->whatsappApi->sendText(
                        $phoneNumber,
                        $campaign['message']
                    );
                    break;

                case 'media':
                    $result = $this->whatsappApi->sendMedia(
                        $phoneNumber,
                        $campaign['media_type'],
                        $campaign['media_url'],
                        $campaign['media_caption']
                    );
                    break;

                case 'interactive':
                    $interactiveData = json_decode($campaign['interactive_data'], true);
                    $result = $this->whatsappApi->sendInteractive(
                        $phoneNumber,
                        $campaign['message'],
                        $interactiveData['type'],
                        $interactiveData['action']
                    );
                    break;
            }

            $this->saveMessage(
                $campaign['user_id'],
                $phoneNumber,
                $campaign,
                $conversationId,
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

    private function validateParams($params) {
        $this->errors = [];
        
        // Existing validation
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
        }

        // Channel-specific validation
        if ($params['channel'] === 'sms' && strlen($params['campaignmessage']) > 160) {
            $this->errors['campaignmessage'] = 'SMS message cannot exceed 160 characters';
        }

        // WhatsApp-specific validation
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

        // Rest of your existing validation...
        return empty($this->errors);
    }

    // Rest of your existing methods...
}