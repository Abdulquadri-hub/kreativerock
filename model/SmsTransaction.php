<?php


class SmsTransaction  {
    
    public $model;
    private $db;
    private $usersTable = 'users';
    private $smsTransactionsTable = 'sms_transactions';
    private $userUnitsTable = 'user_units';
    private $smsPurchasesTable = 'sms_purchases';
    
    public function __construct(){
        $this->model = new Model();
        $this->db = new dbFunctions();
    }
    
    public function getSmsTransactionInfo($condition){
        return $this->model->findOne($this->smsTransactionsTable, $condition);
    }
    public function checkIfSmsTransactionExists($condition){
        return count($this->model->findOne($this->smsTransactionsTable, $condition)) > 0 ? true : false;
    }
    public function registerSmsTransaction($fields, $values){
        return $this->model->insertdata($this->smsTransactionsTable, $fields, $values);
    }
    public function removeSmsTransaction($condition){
        return $this->model->deletedata($this->smsTransactionsTable, $condition);
    }
    public function retrieveAllSmsTransaction($pageno, $limit){
        $data = $this->model->paginate($this->smsTransactionsTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveSmsTransactionByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->smsTransactionsTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveSmsTransactionByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->smsTransactionsTable, $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateSmsTransactionDetails($query, $id){
        return $this->model->update($this->smsTransactionsTable, $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
    public function createOrUpdateUserSMSUnits($reference, $email){
        
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        
        $smsPurchased = $this->db->find($this->smsPurchasesTable, "transactionref = '$reference'");
        if(!$smsPurchased){
            return ['status' => false, 'code' => 404, 'message' => 'Sms purchased package not found.'];
        }
        
        $smsTransaction = $this->db->find($this->smsTransactionsTable, "reference = '$reference' AND status = 'COMPLETED'");
        if(!$smsTransaction){
            return ['status' => false, 'code' => 404, 'message' => 'Sms purchased package transaction not found.'];
        }
        
        $userUnit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'sms'");
        
        $qtyPurchased = $smsPurchased['qty'] ?? 0;
        $qtyUsed = $smsTransaction['qtyout'] ?? 0;
        $amountSpent = $smsPurchased['amount'] ?? 0.00;
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
                'type' => 'sms'
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
	    $postdata["redirect_url"] = $redirectTo. "sms/verifychekoutsms?reference=$ref&packageid=$packageid&callback=$callbackUrl";
	    
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
    
}