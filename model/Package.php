<?php

class Package extends dbFunctions  {

    public $model;
    protected $packagesTable = "packages";
    
    public function __construct(){
        parent::__construct();
        $this->model = new Model();
    }
    
    public function getPackageInfo($condition){
        return $this->model->findOne($this->packagesTable, $condition);
    }
    public function checkIfPackageExists($condition){
        return $this->model->findOne($this->packagesTable, $condition)? true : false;
    }
    public function registerPackage($fields, $values){
        return $this->model->insertdata($this->packagesTable, $fields, $values);
    }
    public function removePackage($condition){
        return $this->model->deletedata($this->packagesTable, $condition);
    }
    public function retrieveAllPackage($pageno, $limit){
        $data = $this->model->paginate($this->packagesTable, " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }
    public function retrievePackageByStatus($status, $pageno, $limit){
        $data = $this->model->paginate($this->packagesTable, "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrievePackageByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate($this->packagesTable, $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updatePackageDetails($query, $id){
        return $this->model->update($this->packagesTable, $query, "WHERE id = $id");
    } 
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }
    
}