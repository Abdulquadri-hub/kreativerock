<?php
class Migrate{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }
    
    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function executeByQuerySelector($query){
        $data = $this->model->exec_query($query);
        return $data;
    }
}
?>