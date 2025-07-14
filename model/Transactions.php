<?php

class Transactions extends dbFunctions {
    
    private $usersTable = 'users';
    private $rolesAndPermissions;
    public $model;
    private $db;
    private $transactionsTable = 'transactions';
    private $userUnitsTable = 'user_units';
    private $purchasesTable = 'purchases';
    
    public function __construct(){
        parent::__construct();
        $this->rolesAndPermissions = new RolesAndPermissions();
        $this->model = new Model();
        $this->db = new dbFunctions();
    }
        
    public function getTransactionInfo($condition){
        return $this->model->findOne($this->transactionsTable, $condition);
    }

    public function checkIfTransactionExists($condition){
        return count($this->model->findOne($this->transactionsTable, $condition)) > 0 ? true : false;
    }

    public function registerTransaction($fields, $values){
        return $this->model->insertdata($this->transactionsTable, $fields, $values);
    }

    public function removeTransaction($condition){
        return $this->model->deletedata($this->transactionsTable, $condition);
    }

    public function retrieveAllTransaction($pageno, $limit){
        $data = $this->model->paginate($this->transactionsTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }

    public function retrieveTransactionByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->transactionsTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    } 

    public function retrieveTransactionByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->transactionsTable, $query, $pageno, $limit,$field);
        return $data;
    }  
      
    public function updateTransactionDetails($query, $id){
        return $this->model->update($this->transactionsTable, $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
    public function createOrUpdateUserUnits($reference, $email){
        
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        
        $purchased = $this->db->find($this->purchasesTable, "transactionref = '$reference'");
        if(!$purchased){
            return ['status' => false, 'code' => 404, 'message' => 'Purchased package not found.'];
        }
        
        $Transaction = $this->db->find($this->transactionsTable, "reference = '$reference' AND status = 'COMPLETED'");
        if(!$Transaction){
            return ['status' => false, 'code' => 404, 'message' => 'Purchased package transaction not found.'];
        }
        
        $userUnit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}'");
        
        $qtyPurchased = $purchased['qty'] ?? 0;
        $qtyUsed = $Transaction['qtyout'] ?? 0;
        $amountSpent = $purchased['amount'] ?? 0.00;
        $currentDate = date('Y-m-d H:i:s');

        if (!empty($userUnit)) {
            
            $updatedTotalPurchased = $userUnit['total_purchased_qty'] + $qtyPurchased;
            $updatedTotalUsed = $userUnit['total_used_qty'] + $qtyUsed;
            $updatedTotalAmountSpent = $userUnit['total_amount_spent'] + $amountSpent;

             $updateData = [
                'total_purchased_qty' => $updatedTotalPurchased,
                'total_used_qty' => $updatedTotalUsed,
                'total_amount_spent' => $updatedTotalAmountSpent,
                'last_transaction_date' => $currentDate,
                'updated_at' => $currentDate,
                'type' => 'sms'
            ];
    
            $this->db->update($this->userUnitsTable, $updateData, "user_id = '{$user['id']}' AND type = 'sms'");
        } else {
       
            $insertData = [
                'user_id' => $user['id'],
                'total_purchased_qty' => $qtyPurchased,
                'total_used_qty' => $qtyUsed,
                'total_amount_spent' => $amountSpent,
                'last_transaction_date' => $currentDate,
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
            ];
            
            $this->db->insert($this->userUnitsTable, $insertData);
        }
    }
    
    public function makePayment($ref,$amount,$email,$phone,$customername,$currency,$skey,$packageid,$callbackUrl)
    { 
        
	    $headers = array("Authorization: Bearer $skey","Content-Type: application/json");
	    $postdata = array(
	       "tx_ref" => "", 
		   "amount" => "", 
		   "email" => "",
		   "currency" => "",
		   "customer" => "",
		   "redirect_url" => ""
	    );  
	    
	    $redirectTo ="https://comeandsee.com.ng/kreativerock/admin/controllers/";
	
	    $customer = array("email"=>$email,"name"=>$customername, "phonenumber"=>$phone);
	    $postdata["tx_ref"] = $ref;
	    $postdata["amount"] = $amount;
	    $postdata["email"] = $email;
	    $postdata["currency"] = $currency;
	    $postdata["customer"] = $customer;
	    $postdata["redirect_url"] = $redirectTo. "units/verifycheckout?reference=$ref&packageid=$packageid&callback=$callbackUrl";
	    
	    //"channels" => ["card,bank"]
	    // $durl = $url; //."?".$getdata;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/payments");
	   // curl_setopt($ch, CURLOPT_HEADER, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
	   //curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt ($ch, CURLOPT_POST, 1); 
	   curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	
	   $result = curl_exec($ch);
	   curl_close($ch);
	   return $result;    
    } 
    
    public function verifyPayment($transaction_id,$skey)
    {
        $headers = array("Authorization: Bearer $skey");
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;    
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
        
        if ($transactionType === 'all') {
            return $this->getTransactions($filters);
        } 
    }

    private function extractFilters($request){
        return [
            'type' => isset($request['type']) && in_array($request['type'], ['all']) 
                     ? $request['type'] : 'all',
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

    private function getTransactions($filters){

        $allQuery = "SELECT * FROM {$this->transactionsTable}";
        $whereConditions = $this->buildWhereConditions($filters);
        
        if (!empty($whereConditions)) {
            $allQuery .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        $combinedQuery = "({$allQuery}) ORDER BY id DESC LIMIT {$filters['limit']} OFFSET {$filters['offset']}";
        
        $result = $this->query($combinedQuery);
        
        if ($result) {
            return [
                'status' => true,
                'code' => 200,
                'message' => 'Combined transactions retrieved successfully',
                'data' => $result,
                'type' => 'all'
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
}