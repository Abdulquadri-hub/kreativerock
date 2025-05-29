<?php


class SmsPackage  {
    
    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }
    
    public function getSmsPackageInfo($condition){
        return $this->model->findOne("sms_packages", $condition);
    }
    public function checkIfSmsPackageExists($condition){
        return $this->model->findOne("sms_packages", $condition)? true : false;
    }
    public function registerSmsPackage($fields, $values){
        return $this->model->insertdata("sms_packages", $fields, $values);
    }
    public function removeSmsPackage($condition){
        return $this->model->deletedata("sms_packages", $condition);
    }
    public function retrieveAllSmsPackage($pageno, $limit){
        $data = $this->model->paginate("sms_packages", " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveSmsPackageByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("sms_packages", "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveSmsPackageByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("sms_packages", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateSmsPackageDetails($query, $id){
        return $this->model->update('sms_packages', $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
}