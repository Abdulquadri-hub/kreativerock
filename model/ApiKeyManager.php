<?php 

class ApiKeyManager {
    
    private $db;
    private $table = 'api_users';
    private $jwtSecretKey = '!@^&%(*&^%)kreativerock!@#$%^&*jwt$%$secret&*&^^%key';
    
    public function __construct(){
        $this->db = new dbFunctions();
    }
    
    public function generateKeys(){
        $publicKey = bin2hex(random_bytes(32));
        $secretKey = bin2hex(random_bytes(32));
        
        $hashedSecretKey = password_hash($secretKey, PASSWORD_ARGON2ID);
        $apiUserId = $this->db->insert($this->table, [
            'public_key' => $publicKey,
            'secret_key' => $hashedSecretKey
        ]);
        
        if($apiUserId > 0){
            return [
                'public_key' => $publicKey,
                'secret_key' => $secretKey
            ];
        }else {
            return null;
        }
    }
    
    public function validateKeys($publicKey, $secretKey){
        $apiUser = $this->db->select($this->table, 'id, secret_key', "public_key = '$publicKey'");
        if (!$apiUser) {
            return false;
        }
        if($apiUser && is_array($apiUser) && password_verify($secretKey, $apiUser['secret_key'])){
            return $apiUser['id'];
        }
    }
    
    public function generateBearer($apiUserId){
        $header = json_encode(['typ' => 'jwt', 'alg' => 'HS256']);
        $payload = json_encode([
            'api_user_id' => $apiUserId, 
            'exp' => time() + 3600, // 1 hour
        ]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->jwtSecretKey);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $result = json_decode($payload, true);
        return ['token' => $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature, 'exp' => $result['exp']];
        
    }
    
    public function validateBearer($token) {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return false;
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);

        if ($payload === null || !isset($payload['api_user_id']) || !isset($payload['exp'])) {
            return false;
        }

        if ($payload['exp'] < time()) {
            return false;
        }

        $signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], $this->jwtSecretKey);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if ($base64UrlSignature !== $tokenParts[2]) {
            echo "yes";
            return false;
        }

        return $payload['api_user_id'];
    }
    
    
    
    
}