<?php


class WhatsAppTransaction  {
    
    public $model;
    private $db;
    private $usersTable = 'users';
    private $whatsappTransactionsTable = 'whatsapp_transactions';
    private $userUnitsTable = 'user_units';
    private $whatsappPurchasesTable = 'whatsapp_purchases';
    
    public function __construct(){
        $this->model = new Model();
        $this->db = new dbFunctions();
    }
    
    public function getWhatsAppTransactionInfo($condition){
        return $this->model->findOne($this->whatsappTransactionsTable, $condition);
    }
    public function checkIfWhatsAppTransactionExists($condition){
        return count($this->model->findOne($this->whatsappTransactionsTable, $condition)) > 0 ? true : false;
    }
    public function registerWhatsAppTransaction($fields, $values){
        return $this->model->insertdata($this->whatsappTransactionsTable, $fields, $values);
    }
    public function removeWhatsAppTransaction($condition){
        return $this->model->deletedata($this->whatsappTransactionsTable, $condition);
    }
    public function retrieveAllWhatsAppTransaction($pageno, $limit){
        $data = $this->model->paginate($this->whatsappTransactionsTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveWhatsAppTransactionByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->whatsappTransactionsTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveWhatsAppTransactionByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->whatsappTransactionsTable, $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateWhatsAppTransactionDetails($query, $id){
        return $this->model->update($this->whatsappTransactionsTable, $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
    public function createOrUpdateUserWhatsappUnits($reference, $email){
        
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(count($user) < 0 || empty($user)){
            return ['status' => false, 'code' => 404, 'message' => 'User not found.'];
        }
        
        $smsPurchased = $this->db->find($this->whatsappPurchasesTable, "transactionref = '$reference'");
        if(!$smsPurchased){
            return ['status' => false, 'code' => 404, 'message' => 'Sms purchased package not found.'];
        }
        
        $WhatsAppTransaction = $this->db->find($this->whatsappTransactionsTable, "reference = '$reference' AND status = 'COMPLETED'");
        if(!$WhatsAppTransaction){
            return ['status' => false, 'code' => 404, 'message' => 'Sms purchased package transaction not found.'];
        }
        
        $userUnit = $this->db->find($this->userUnitsTable, "user_id = '{$user['id']}' AND type = 'whatsapp'");
        
        $qtyPurchased = $smsPurchased['qty'] ?? 0;
        $qtyUsed = $WhatsAppTransaction['qtyout'] ?? 0;
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
                'type' => 'whatsapp'
            ];
    
            $this->db->update($this->userUnitsTable, $updateData, "user_id = '{$user['id']}' AND type = 'whatsapp'");
        } else {
       
            $insertData = [
                'user_id' => $user['id'],
                'total_purchased_qty' => $qtyPurchased,
                'total_used_qty' => $qtyUsed,
                'total_amount_spent' => $amountSpent,
                'last_transaction_date' => $currentDate,
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
                'type' => 'whatsapp'
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
	    $postdata["redirect_url"] = $redirectTo. "whatsapp/verifycheckout?reference=$ref&packageid=$packageid&callback=$callbackUrl";
	    
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