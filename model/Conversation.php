<?php

class Conversation {
    private $db;
    private $messagesTable = 'messages';
    private $rcsUsersTable = 'rcs_users';
    private $conversationsTable = 'conversations';
    private $keyWordsTable = 'keywords';
    private $promptsTable = 'prompts';
    private $smsTransactionsTable = 'sms_transactions';
    private $smsPackagesTable = 'sms_packages';
    private $usersTable = 'users';

    public function __construct() {
        $this->db = new dbFunctions();
    }

    public function getCampaignUsersList($campaignId, $email) {
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        $senderId = $user['id'];

        $query = "
            WITH RankedMessages AS (
                SELECT 
                    m.*,
                    r.phone_number,
                    r.id as rcs_userid,
                    sc.id as sms_campaign_id,
                    cm.position,
                    ROW_NUMBER() OVER (
                        PARTITION BY r.phone_number 
                        ORDER BY m.created_at DESC
                    ) as rn
                FROM campaign_messages cm
                JOIN messages m ON cm.message_id = m.message_id
                LEFT JOIN rcs_users r ON m.destinations = r.phone_number
                JOIN sms_campaigns sc ON sc.id = cm.campaign_id
                WHERE cm.campaign_id = ?
                AND m.user_id = ?
                AND (cm.position = 'others')
            )
            SELECT 
                phone_number,
                sms_campaign_id,
                rcs_userid as rcs_user_id,
                message_id,
                content as last_message,
                user_id as sender_id,
                direction,
                status,
                created_at,
                conversation_id,
                position
            FROM RankedMessages 
            WHERE rn = 1
            ORDER BY created_at DESC";

        try {
            $result = $this->db->query($query, [$campaignId, $senderId]);
            if (!$result) {
                throw new Exception("Failed to fetch campaign users");
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error fetching campaign users: " . $e->getMessage());
            return [];
        }
    }

    public function getMessagesForUser($campaignId, $phoneNumber, $email) {
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        $senderId = $user['id'];
        
        $query = "
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
                sc.id as sms_campaign_id,
                cm.position
            FROM campaign_messages cm
            JOIN messages m ON cm.message_id = m.message_id
            LEFT JOIN conversations c ON m.conversation_id = c.conversation_id
            LEFT JOIN rcs_users r ON m.destinations = r.phone_number
            JOIN sms_campaigns sc ON sc.id = cm.campaign_id
            WHERE 
                m.status != 'failed'
                AND cm.campaign_id = ? 
                AND r.phone_number = ?
                AND m.user_id = ?
                AND (
                    cm.position = 'first'
                    OR 
                    (cm.position = 'others' AND EXISTS (
                        SELECT 1 
                        FROM campaign_messages first_msg 
                        WHERE first_msg.campaign_id = cm.campaign_id 
                        AND first_msg.position = 'first'
                        AND first_msg.message_id IN (
                            SELECT message_id 
                            FROM messages 
                            WHERE destinations = r.phone_number
                        )
                    ))
                )
            ORDER BY m.created_at DESC";

        try {
            $result = $this->db->query($query, [$campaignId, $phoneNumber, $senderId]);
            if (!$result) {
                throw new Exception("Failed to fetch user messages");
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error fetching user messages: " . $e->getMessage());
        }
    }
}