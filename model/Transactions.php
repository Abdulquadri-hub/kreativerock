<?php

class Transactions extends dbFunctions {
    
    private $usersTable = 'users';
    private $whatsappTransactionsTable = 'whatsapp_transactions';
    private $smsTransactionsTable = 'sms_transactions';
    private $rolesAndPermissions;

    public function __construct(){
        parent::__construct();
        $this->rolesAndPermissions = new RolesAndPermissions();
    }

    public function getAllTransactions($request, string $email){
        $email = $this->escape($email);
        $user = $this->find($this->usersTable, "email = '$email'");
        
        if (!$user) {
            return badRequest(400, 'User not found');
        }

        $isSuperAdmin = $this->rolesAndPermissions->hasRole($user['id'], "SUPERADMIN");
        if(!$isSuperAdmin){
            return badRequest(403, "You do not have access to this feature");
        }

        $filters = $this->extractFilters($request);
        
        $transactionType = $filters['type'];
        
        if ($transactionType === 'both') {
            return $this->getCombinedTransactions($filters);
        } elseif ($transactionType === 'whatsapp') {
            return $this->getWhatsAppTransactions($filters);
        } elseif ($transactionType === 'sms') {
            return $this->getSmsTransactions($filters);
        } else {
            return badRequest(400, 'Invalid transaction type specified');
        }
    }

    private function extractFilters($request){
        return [
            'type' => isset($request['type']) && in_array($request['type'], ['whatsapp', 'sms', 'both']) 
                     ? $request['type'] : 'both',
            'status' => isset($request['status']) && $request['status'] !== "" 
                       ? $this->escape($request['status']) : "",
            'reference' => isset($request['reference']) && $request['reference'] !== "" 
                          ? $this->escape($request['reference']) : "",
            'user' => isset($request['user']) && $request['user'] !== "" 
                     ? $this->escape($request['user']) : "",
            'start_date' => isset($request['start_date']) && $request['start_date'] !== "" 
                           ? $request['start_date'] : "",
            'end_date' => isset($request['end_date']) && $request['end_date'] !== "" 
                         ? $request['end_date'] : date("Y-m-d H:i:s"),
            'limit' => isset($request['limit']) && is_numeric($request['limit']) 
                      ? (int)$request['limit'] : 50,
            'offset' => isset($request['offset']) && is_numeric($request['offset']) 
                       ? (int)$request['offset'] : 0
        ];
    }

    private function getWhatsAppTransactions($filters){
        $query = "SELECT *, 'whatsapp' as transaction_type FROM {$this->whatsappTransactionsTable}";
        $whereConditions = $this->buildWhereConditions($filters);
        
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        $query .= " ORDER BY id DESC LIMIT {$filters['limit']} OFFSET {$filters['offset']}";
        
        $result = $this->query($query);
        
        if ($result) {
            return [
                'status' => true,
                'code' => 200,
                'message' => 'WhatsApp transactions retrieved successfully',
                'data' => $result,
                'type' => 'whatsapp'
            ];
        } else {
            return badRequest(204, 'No WhatsApp transactions found');
        }
    }

    private function getSmsTransactions($filters){
        $query = "SELECT *, 'sms' as transaction_type FROM {$this->smsTransactionsTable}";
        $whereConditions = $this->buildWhereConditions($filters);
        
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        $query .= " ORDER BY id DESC LIMIT {$filters['limit']} OFFSET {$filters['offset']}";
        
        $result = $this->query($query);
        
        if ($result) {
            return [
                'status' => true,
                'code' => 200,
                'message' => 'SMS transactions retrieved successfully',
                'data' => $result,
                'type' => 'sms'
            ];
        } else {
            return badRequest(204, 'No SMS transactions found');
        }
    }

    private function getCombinedTransactions($filters){
        // Get WhatsApp transactions
        $whatsappQuery = "SELECT *, 'whatsapp' as transaction_type FROM {$this->whatsappTransactionsTable}";
        $whereConditions = $this->buildWhereConditions($filters);
        
        if (!empty($whereConditions)) {
            $whatsappQuery .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Get SMS transactions
        $smsQuery = "SELECT *, 'sms' as transaction_type FROM {$this->smsTransactionsTable}";
        
        if (!empty($whereConditions)) {
            $smsQuery .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Combine queries with UNION and apply ordering and limit
        $combinedQuery = "({$whatsappQuery}) UNION ALL ({$smsQuery}) ORDER BY id DESC LIMIT {$filters['limit']} OFFSET {$filters['offset']}";
        
        $result = $this->query($combinedQuery);
        
        if ($result) {
            return [
                'status' => true,
                'code' => 200,
                'message' => 'Combined transactions retrieved successfully',
                'data' => $result,
                'type' => 'combined'
            ];
        } else {
            return badRequest(204, 'No transactions found');
        }
    }

    private function buildWhereConditions($filters){
        $conditions = [];
        
        // Status filter
        if (!empty($filters['status'])) {
            $conditions[] = "status = '{$filters['status']}'";
        }
        
        // Reference filter
        if (!empty($filters['reference'])) {
            $conditions[] = "reference = '{$filters['reference']}'";
        }
        
        // User filter
        if (!empty($filters['user'])) {
            $conditions[] = "user = '{$filters['user']}'";
        }
        
        // Date range filter
        if (!empty($filters['start_date'])) {
            $conditions[] = "created_at BETWEEN '{$filters['start_date']}' AND '{$filters['end_date']}'";
        }
        
        return $conditions;
    }

    public function getTransactionStats($email){
        $email = $this->escape($email);
        $user = $this->find($this->usersTable, "email = '$email'");
        
        if (!$user) {
            return badRequest(400, 'User not found');
        }

        $isSuperAdmin = $this->rolesAndPermissions->hasRole($user['id'], "SUPERADMIN");
        if(!$isSuperAdmin){
            return badRequest(403, "You do not have access to this feature");
        }

        // Get WhatsApp stats
        $whatsappStats = $this->query("
            SELECT 
                COUNT(*) as total_count,
                COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) as completed_count,
                COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'FAILED' THEN 1 END) as failed_count,
                SUM(CASE WHEN status = 'COMPLETED' THEN amount ELSE 0 END) as total_amount
            FROM {$this->whatsappTransactionsTable}
        ");

        // Get SMS stats
        $smsStats = $this->query("
            SELECT 
                COUNT(*) as total_count,
                COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) as completed_count,
                COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'FAILED' THEN 1 END) as failed_count,
                SUM(CASE WHEN status = 'COMPLETED' THEN amount ELSE 0 END) as total_amount
            FROM {$this->smsTransactionsTable}
        ");

        return [
            'status' => true,
            'code' => 200,
            'message' => 'Transaction statistics retrieved successfully',
            'data' => [
                'whatsapp' => $whatsappStats[0] ?? null,
                'sms' => $smsStats[0] ?? null,
                'combined' => [
                    'total_count' => ($whatsappStats[0]['total_count'] ?? 0) + ($smsStats[0]['total_count'] ?? 0),
                    'completed_count' => ($whatsappStats[0]['completed_count'] ?? 0) + ($smsStats[0]['completed_count'] ?? 0),
                    'pending_count' => ($whatsappStats[0]['pending_count'] ?? 0) + ($smsStats[0]['pending_count'] ?? 0),
                    'failed_count' => ($whatsappStats[0]['failed_count'] ?? 0) + ($smsStats[0]['failed_count'] ?? 0),
                    'total_amount' => ($whatsappStats[0]['total_amount'] ?? 0) + ($smsStats[0]['total_amount'] ?? 0)
                ]
            ]
        ];
    }
}