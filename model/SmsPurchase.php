<?php


class SmsPurchase  {
    
    public $model;
    private $purchases;
    
    public function __construct(){
        $this->model = new Model();
    }
    
    public function getSmsPurchaseInfo($condition){
        return $this->model->findOne("sms_purchases", $condition);
    }
    public function checkIfSmsPurchaseExists($condition){ 
        return count($this->model->findOne("sms_purchases", $condition)) > 0 ? true : false;
    }
    public function registerSmsPurchase($fields, $values){
        return $this->model->insertdata("sms_purchases", $fields, $values);
    }
    public function removeSmsPurchase($condition){
        return $this->model->deletedata("sms_purchases", $condition);
    }
    public function retrieveAllSmsPurchase($pageno, $limit){
        $data = $this->model->paginate("sms_purchases", " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveSmsPurchaseByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("sms_purchases", "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveSmsPurchaseByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("sms_purchases", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateSmsPurchaseDetails($query, $id){
        return $this->model->update('sms_purchases', $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
}