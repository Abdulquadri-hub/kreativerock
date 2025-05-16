<?php


class WhatsAppPurchase  {
    
    public $model;
    private $whatsappPurchasesTable = 'whatsapp_purchases';
    
    public function __construct(){
        $this->model = new Model();
    }
    
    public function getWhatsAppPurchaseInfo($condition){
        return $this->model->findOne($this->whatsappPurchasesTable, $condition);
    }
    public function checkIfWhatsAppPurchaseExists($condition){ 
        return count($this->model->findOne($this->whatsappPurchasesTable, $condition)) > 0 ? true : false;
    }
    public function registerWhatsAppPurchase($fields, $values){
        return $this->model->insertdata($this->whatsappPurchasesTable, $fields, $values);
    }
    public function removeWhatsAppPurchase($condition){
        return $this->model->deletedata($this->whatsappPurchasesTable, $condition);
    }
    public function retrieveAllWhatsAppPurchase($pageno, $limit){
        $data = $this->model->paginate($this->whatsappPurchasesTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveWhatsAppPurchaseByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->whatsappPurchasesTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveWhatsAppPurchaseByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->whatsappPurchasesTable, $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateWhatsAppPurchaseDetails($query, $id){
        return $this->model->update($this->whatsappPurchasesTable, $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
}