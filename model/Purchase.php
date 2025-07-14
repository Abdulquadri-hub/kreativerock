<?php

class Purchase extends dbFunctions {

    public $model;
    protected $purchasesTable = "purchases";
    
    public function __construct(){
        parent::__construct();
        $this->model = new Model();
    }
    
    public function getPurchaseInfo($condition){
        return $this->model->findOne($this->purchasesTable, $condition);
    }
    public function checkIfPurchaseExists($condition){ 
        return count($this->model->findOne($this->purchasesTable, $condition)) > 0 ? true : false;
    }
    public function registerPurchase($fields, $values){
        return $this->model->insertdata($this->purchasesTable, $fields, $values);
    }
    public function removePurchase($condition){
        return $this->model->deletedata($this->purchasesTable, $condition);
    }
    public function retrieveAllPurchase($pageno, $limit){
        $data = $this->model->paginate($this->purchasesTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrievePurchaseByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->purchasesTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrievePurchaseByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->purchasesTable, $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updatePurchaseDetails($query, $id){
        return $this->model->update('sms_purchases', $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
}