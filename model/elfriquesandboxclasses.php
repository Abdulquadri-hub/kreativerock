<?php
class ElfriqueSandBoxClasses{

    public $modelsandbox;
    
    public function __construct(){
        $this->modelsandbox = new ModelSandBox();
    }
    
    public function getRowsNumber($table){
        return count($this->modelsandbox->findAll($table));
    }

    public function executeByQuerySelector($query){
        $data = $this->modelsandbox->exec_query($query);
        return $data;
    }
}
?>