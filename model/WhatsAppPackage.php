<?php


class WhatsAppPackage  {
    
    public $model;
    private $whatsappPackagesTable = "whatsapp_packages";
    
    public function __construct(){
        $this->model = new Model();
    }
    
    public function getWhatsAppPackageInfo($condition){
        return $this->model->findOne($this->whatsappPackagesTable, $condition);
    }
    public function checkIfWhatsAppPackageExists($condition){
        return count($this->model->findOne($this->whatsappPackagesTable, $condition)) > 0 ? true : false;
    }
    public function registerWhatsAppPackage($fields, $values){
        return $this->model->insertdata($this->whatsappPackagesTable, $fields, $values);
    }
    public function removeWhatsAppPackage($condition){
        return $this->model->deletedata($this->whatsappPackagesTable, $condition);
    }
    public function retrieveAllWhatsAppPackage($pageno, $limit){
        $data = $this->model->paginate($this->whatsappPackagesTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveWhatsAppPackageByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->whatsappPackagesTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveWhatsAppPackageByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->whatsappPackagesTable, $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateWhatsAppPackageDetails($query, $id){
        return $this->model->update($this->whatsappPackagesTable, $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
}