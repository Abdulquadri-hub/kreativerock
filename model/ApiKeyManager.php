<?php

class ApiKeyManager {
    private $db;
    private $table = 'users';
    private const KEY_LENGTH = 15; 
    private const MAX_FAILED_ATTEMPTS = 5; 
    private const RATE_LIMIT_WINDOW = 300; 
    
    public function __construct(){
        $this->db = new dbFunctions();
    }
    
    public function generateApiKey($userId){
        try {
            // Generate a more secure API key with prefix for easy identification
            $randomBytes = random_bytes(self::KEY_LENGTH);
            $apiKey = 'kr_' . bin2hex($randomBytes);
            
            // Store the hashed API key using more secure hashing
            // $hashedApiKey = password_hash($apiKey, PASSWORD_ARGON2ID, [
            //     'memory_cost' => 65536,
            //     'time_cost' => 4,
            //     'threads' => 3
            // ]);
            
            $updated = $this->db->update($this->table, [
                'api_key' => $apiKey,
                'api_key_generated_at' => date('Y-m-d H:i:s'),
                'api_key_last_used' => null,
                'failed_auth_attempts' => 0
            ], "id = '{$userId}'");
            
            if (!$updated) {
                return [
                    "status" => false,
                    "message" => "Failed to update user with new API key"
                ];
            }
            
            return $apiKey;
            
        } catch (Exception $e) {
            error_log("API Key Generation Error: " . $e->getMessage());
            return null;
        }
    }
    
    public function validateApiKey($apiKey){
        try {
            // Get user with attempted validation
            $user = $this->db->select($this->table, 
                'id, api_key, failed_auth_attempts, last_failed_attempt', 
                "api_key IS NOT NULL"
            );
            
            if (!$user) {
                return false;
            }
            
            foreach ($user as $u) {
                // Check rate limiting for failed attempts
                if ($this->isRateLimited($u)) {
                    return [
                        "status" => false,
                        "message" => "Too many failed attempts. Try again later."
                    ];
                }
                
                if ($apiKey === $u['api_key']) {
                    $this->updateAuthStatus($u['id'], true);
                    return $u['id'];
                }
            }
            
            // Increment failed attempts
            $this->updateAuthStatus($user['id'], false);
            return false;
            
        } catch (Exception $e) {
            error_log("API Key Validation Error: " . $e->getMessage());
            return false;
        }
    }
    
    private function isRateLimited($user) {
        if ($user['failed_auth_attempts'] >= self::MAX_FAILED_ATTEMPTS) {
            $lastFailedTime = strtotime($user['last_failed_attempt']);
            if (time() - $lastFailedTime < self::RATE_LIMIT_WINDOW) {
                return true;
            }
            // Reset attempts after window expires
            $this->resetFailedAttempts($user['id']);
        }
        return false;
    }

    private function updateAuthStatus($userId, $success) {
        if ($success) {
            // On successful auth, reset failed attempts and update last used time
            $data = [
                'failed_auth_attempts' => 0, 
                'api_key_last_used' => date('Y-m-d H:i:s')
            ];
        } else {
            // On failed auth, get current failed attempts and increment
            $user = $this->db->select($this->table, 'failed_auth_attempts', "id = '$userId'");
            $currentAttempts = isset($user['failed_auth_attempts']) ? $user['failed_auth_attempts'] : 0;
            
            $data = [
                'failed_auth_attempts' => $currentAttempts + 1,
                'last_failed_attempt' => date('Y-m-d H:i:s')
            ];
        }
        
        $this->db->update($this->table, $data, "id = '$userId'");
    }
    
    private function resetFailedAttempts($userId) {
        $this->db->update($this->table, ['failed_auth_attempts' => 0], "id = '{$userId}'");
    }
}

