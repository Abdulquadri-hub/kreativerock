<?php
class Administration{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }

    /** Products ***************************************************************************/
    
    public function getProductInfo($condition){
        return $this->model->findOne("products", $condition);
    }
    public function checkIfProductExists($condition){
        return count($this->model->findOne("products", $condition)) > 0 ? true : false;
    }
    public function registerProduct($fields, $values){
        return $this->model->insertdata("products", $fields, $values);
    }
    public function retrieveAllProduct($pageno, $limit){
        $data = $this->model->paginate("products", " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveProductByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("products", "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveProductByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("products", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateProductDetails($query, $id){
        return $this->model->update('products', $query, "WHERE id = $id");
    }    
    public function getLastID(){
        return $this->model->lastId();
    }
    /** End of Products *****************************************************************************************************/
    
    /** Transactions ***************************************************************************/
    
    public function getTransactionInfo($condition){
        return $this->model->findOne("transactions", $condition);
    }
    public function registerTransaction($fields, $values){
        return $this->model->insertdata("transactions", $fields, $values);
    }
    public function updateTransactionDetails($query, $id){
        return $this->model->update('transactions', $query, "WHERE id = $id");
    }    
    
    /** End of Transactions *****************************************************************************************************/

    /** ProductItems ***************************************************************************/
    
    public function getProductItemInfo($condition){
        return $this->model->findOne("productitems", $condition);
    }
    public function registerProductItem($fields, $values){
        return $this->model->insertdata("productitems", $fields, $values);
    }
    public function updateProductItemDetails($query, $id){
        return $this->model->update('productitems', $query, "WHERE id = $id");
    }    
    /** End of Transactions *****************************************************************************************************/

    /** ProductItems ***************************************************************************/
    
    public function getTicketInfo($condition){
        return $this->model->findOne("ticket", $condition);
    }
    public function registerTicket($fields, $values){
        return $this->model->insertdata("ticket", $fields, $values);
    }
    public function updateTicketDetails($query, $id){
        return $this->model->update('ticket', $query, "WHERE id = $id");
    }    
    /** End of Transactions *****************************************************************************************************/    

    /** Wallet *****************************************************************************************************/
    public function getWalletInfo($condition){
        return $this->model->findOne("wallet", $condition);
    }
    public function registerWallet($fields, $values){
        return $this->model->insertdata("wallet", $fields, $values);
    }
    public function updateWalletDetails($query, $id){
        return $this->model->update('wallet', $query, "WHERE id = $id");
    }    
    /** End of Wallet *****************************************************************************************************/ 

    /** Voting *****************************************************************************************************/
    public function getVotingInfo($condition){
        return $this->model->findOne("voting", $condition);
    }
    public function registerVoting($fields, $values){
        return $this->model->insertdata("voting", $fields, $values);
    }
    public function updateVotingDetails($query, $id){
        return $this->model->update('voting', $query, "WHERE id = $id");
    }    
    /** End of Voting *****************************************************************************************************/     

    /** Trivia Pay *****************************************************************************************************/
    public function getTriviaPayInfo($condition){
        return $this->model->findOne("triviapay", $condition);
    }
    public function registerTriviaPay($fields, $values){
        return $this->model->insertdata("triviapay", $fields, $values);
    }
    public function updateTriviaPayDetails($query, $id){
        return $this->model->update('triviapay', $query, "WHERE id = $id");
    }    
    /** End of Voting *****************************************************************************************************/     

    /** Ticket Pay *****************************************************************************************************/
    public function getTicketPayInfo($condition){
        return $this->model->findOne("ticketpay", $condition);
    }
    public function registerTicketPay($fields, $values){
        $result = $this->model->insertdata("ticketpay", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }           
    }
    public function updateTicketPayDetails($query, $id){
        return $this->model->update('ticketpay', $query, "WHERE id = $id");
    }    
    /** End of Ticket Pay *****************************************************************************************************/     

    /** Ticket Numbers *****************************************************************************************************/
    public function getTicketNumbersInfo($condition){
        return $this->model->findOne("ticketnumbers", $condition);
    }
    public function registerTicketNumbers($fields, $values){
        return $this->model->insertdata("ticketnumbers", $fields, $values);
    }
    public function updateTicketNumbersDetails($query, $id){
        return $this->model->update('ticketnumbers', $query, "WHERE id = $id");
    }    
    /** End of Ticket Numbers *****************************************************************************************************/   

    /** Payout *****************************************************************************************************/
    public function getPayoutInfo($condition){
        return $this->model->findOne("payout", $condition);
    }
    public function registerPayout($fields, $values){
        return $this->model->insertdata("payout", $fields, $values);
    }
    public function updatePayoutDetails($query, $id){
        return $this->model->update('payout', $query, "WHERE id = $id");
    }    
    /** End of Payout *****************************************************************************************************/   
    
    /** Form Pay *****************************************************************************************************/
    public function getFormPayInfo($condition){
        return $this->model->findOne("formpay", $condition);
    }
    public function registerFormPay($fields, $values){
        return $this->model->insertdata("formpay", $fields, $values);
    }
    public function updateFormPayDetails($query, $id){
        return $this->model->update('formpay', $query, "WHERE id = $id");
    }    
    /** End of Form Pay *****************************************************************************************************/     
    /** Reviews ******************************************************************************************/
    public function getReviewInfo($condition){
        return $this->model->findOne("reviews", $condition);
    }
    public function registerReview($fields, $values){
        return $this->model->insertdata("reviews", $fields, $values);
    }
    public function updateReviewDetails($query, $id){
        return $this->model->update('reviews', $query, "WHERE id = $id");
    }    
    /** End of Lost and Found Register********************************************************************************************/   
    
    /** Tour Pay *****************************************************************************************************/
    public function getTourPayInfo($condition){
        return $this->model->findOne("tourpay", $condition);
    }
    public function registerTourPay($fields, $values){
        $result = $this->model->insertdata("tourpay", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }          
    }
    public function updateTourPayDetails($query, $id){
        return $this->model->update('tourpay', $query, "WHERE id = $id");
    }    
    /** End of Tour Pay *****************************************************************************************************/  
    
    /** Gateway *****************************************************************************************************/
    public function getFlutterInfo($condition){
        return $this->model->findOne("gateways", $condition);
    }
    /** End Gateway *****************************************************************************************************/
    
    
    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function executeByQuerySelector($query){
        $data = $this->model->exec_query($query);
        return $data;
    }
}
?>