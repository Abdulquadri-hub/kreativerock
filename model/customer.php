<?php
class Customer{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }

    /***** Customer ***************************************************************/
    public function getCustomerInfo($condition){
        return $this->model->findOne("customeraccount", $condition);
    }

    public function getCustomerRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function checkIfCustomerExists($condition){
        return count($this->model->findOne("customeraccount", $condition)) > 0 ? true : false;
    }

    public function registerCustomer($fields, $values){
        return $this->model->insertdata("customeraccount", $fields, $values);
    }

    public function retrieveCustomer($id, $pageno, $limit){
        $res = $this->model->paginate("customeraccount", "id = $id ORDER BY lastname ASC", $pageno, $limit);
        return $res;
    }

    public function getCustomerFields($id, $location, $field){
        return $this->model->findOne("customeraccount", "id = '$id'", "$field");
    }


    public function updateCustomerImage($photourl, $id){
        return $this->model->update('customeraccount', "photourl = '$photourl'", "WHERE id = '$id'");
    }

    public function updateCustomerDetails($query, $id){
        return $this->model->update('customeraccount', "$query", "WHERE id = '$id'");
    }
    public function retrieveCustomerByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("customeraccount", $query, $pageno, $limit,$field);
        return $data;
    }        
    public function getCustomers(){
        return $this->model->findAll("customeraccount");
    }
    /***** End of Customer ***************************************************************/

    /***** Savings Product ***************************************************************/
    public function getSavingsProductInfo($condition){
        return $this->model->findOne("savingsproduct", $condition);
    }

    public function getSavingsProductRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function checkIfSavingsProductExists($condition){
        return count($this->model->findOne("savingsproduct", $condition)) > 0 ? true : false;
    }

    public function registerSavingsProduct($fields, $values){
        return $this->model->insertdata("savingsproduct", $fields, $values);
    }

    public function retrieveSavingsProduct($id, $pageno, $limit){
        $res = $this->model->paginate("savingsproduct", "id = $id ORDER BY productname ASC", $pageno, $limit);
        return $res;
    }

    public function getSavingsProductFields($id, $location, $field){
        return $this->model->findOne("savingsproduct", "id = '$id'", "$field");
    }

    public function updateSavingsProductDetails($query, $id){
        return $this->model->update('savingsproduct', "$query", "WHERE id = '$id'");
    }
    public function retrieveSavingsProductByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("savingsproduct", $query, $pageno, $limit,$field);
        return $data;
    }     
    public function getSavingsProduct(){
        return $this->model->findAll("savingsproduct");
    }
    /***** End of Savings Product ***************************************************************/    
    
    /***** Savings Account ***************************************************************/
    public function getSavingsInfo($condition){
        return $this->model->findOne("savingsaccount", $condition);
    }

    public function getSavingsRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function checkIfSavingsExists($condition){
        return count($this->model->findOne("savingsaccount", $condition)) > 0 ? true : false;
    }

    public function registerSavings($fields, $values){
        return $this->model->insertdata("savingsaccount", $fields, $values);
    }

    public function retrieveSavings($id, $pageno, $limit){
        $res = $this->model->paginate("savingsaccount", "id = $id", $pageno, $limit);
        return $res;
    }

    public function getSavingsFields($id, $location, $field){
        return $this->model->findOne("savingsaccount", "id = '$id'", "$field");
    }

    public function updateSavingsDetails($query, $id){
        return $this->model->update('savingsaccount', "$query", "WHERE id = '$id'");
    }
    public function retrieveSavingsByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("savingsaccount", $query, $pageno, $limit,$field);
        return $data;
    }        
    public function getSavings(){
        return $this->model->findAll("savingsaccount");
    }
    /***** End of Savings Account ***************************************************************/
    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }

}
?>