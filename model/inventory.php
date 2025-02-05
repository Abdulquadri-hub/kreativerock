<?php
class Inventory{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }

    /** Inventory ***************************************************************************/
    
    public function getInventoryInfo($condition){
        return $this->model->findOne("inventory", $condition);
    }
    public function checkIfInventoryExists($condition){
        return count($this->model->findOne("inventory", $condition)) > 0 ? true : false;
    }
    public function registerInventory($fields, $values){
        return $this->model->insertdata("inventory", $fields, $values);
    }
    public function retrieveAllInventory($pageno, $limit){
        $data = $this->model->paginate("inventory", " 1 ORDER BY location ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveInventoryByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("inventory", "status LIKE '$status' ORDER BY itemname ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveInventoryByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("inventory", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateInventoryDetails($query, $itemid){
        return $this->model->update('inventory', $query, "WHERE itemid = $itemid");
    }    
    /** End of Inventory *****************************************************************************************************/
 
     /** Intake Outtake ***************************************************************************/
    
    public function getIntakeOuttake($condition){
        return $this->model->findOne("intakeouttake", $condition);
    }
    public function checkIfIntakeOuttakeExists($condition){
        return count($this->model->findOne("intakeouttake", $condition)) > 0 ? true : false;
    }
    public function registerIntakeOuttake($fields, $values){
        return $this->model->insertdata("intakeouttake", $fields, $values);
    }
    public function retrieveAllIntakeOuttakes($pageno, $limit){
        $data = $this->model->paginate("intakeouttake", " 1 ORDER BY location,transactiondate ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveIntakeOuttakeByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("intakeouttake", "status LIKE '$status' ORDER BY transactiondate ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveIntakeOuttakeByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("intakeouttake", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateIntakeOuttakeDetails($query, $id){
        return $this->model->update('intakeouttake', $query, "WHERE id = $id");
    }    
    /** End of Intake Outtake *****************************************************************************************************/

     /** Inventorydetail ***************************************************************************/
    
    public function getInventoryDetail($condition){
        return $this->model->findOne("inventorydetail", $condition);
    }
    public function registerInventoryDetail($fields, $values){
        return $this->model->insertdata("inventorydetail", $fields, $values);
    }
  
    /** End of Inventorydetail *****************************************************************************************************/

     /** Transaction Parent ***************************************************************************/
    public function registerTransactionParent($fields, $values){
        $res = $this->model->insertdata("transactionparent", $fields, $values);
        if($res){
            return $this->model->lastId();
        }else{
            return null;
        }
    }
  
    /** End of Transaction Parent *****************************************************************************************************/    
    
    /** Item Type ***************************************************************************/
    public function getItemtypeInfo($condition){
        return $this->model->findOne("itemtype", $condition);
    }
    public function checkIfItemtypeExists($condition){
        return count($this->model->findOne("itemtype", $condition)) > 0 ? true : false;
    }
    public function registerItemtype($fields, $values){
        return $this->model->insertdata("itemtype", $fields, $values);
    }
    public function retrieveAllItemtype($pageno, $limit){
        $data = $this->model->paginate("itemtype", " 1 ORDER BY location ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveItemtypeByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("itemtype", "status LIKE '$status' ORDER BY itemtype ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveItemtypeByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("itemtype", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateItemtypeDetails($query, $itemtype){
        return $this->model->update('itemtype', $query, "WHERE itemtype = '$itemtype'");
    }    
    /** End of Item Type *****************************************************************************************************/
      
    /** Composite Item ***************************************************************************/
    public function getCompositeItemInfo($condition){
        return $this->model->findOne("builditems", $condition);
    }
    public function checkIfCompositeItemExists($condition){
        return count($this->model->findOne("builditems", $condition)) > 0 ? true : false;
    }
    public function registerCompositeItem($fields, $values){
        return $this->model->insertdata("builditems", $fields, $values);
    }
    public function retrieveAllCompositeItem($pageno, $limit){
        $data = $this->model->paginate("builditems", " 1 ORDER BY itemtobuildid ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveCompositeItemByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("builditems", "status LIKE '$status'", $pageno, $limit);
        return $data;
    }    
    public function retrieveCompositeItemByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("builditems", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateCompositeItemDetails($query, $compositeitem){
        return $this->model->update('builditems', $query, "WHERE itemtobuildid = $compositeitem");
    }    
    /** End of Composite Item *****************************************************************************************************/

    /** Supplier ***************************************************************************/
    public function getSupplierInfo($condition){
        return $this->model->findOne("supplier", $condition);
    }
    public function checkIfSupplierExists($condition){
        return count($this->model->findOne("supplier", $condition)) > 0 ? true : false;
    }
    public function registerSupplier($fields, $values){
        return $this->model->insertdata("supplier", $fields, $values);
    }
    public function retrieveAllSupplier($pageno, $limit){
        $data = $this->model->paginate("supplier", " 1 ORDER BY companyname ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveSupplierByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("supplier", "status LIKE '$status'", $pageno, $limit);
        return $data;
    }    
    public function retrieveSupplierByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("supplier", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateSupplierDetails($query, $id){
        return $this->model->update('supplier', $query, "WHERE id = $id");
    }    
    /** End of Supplier  ****************************************************************************************************/  
    
     /** Property Items  ***************************************************************************/
    public function checkIfPropertyItemExists($condition){
        return count($this->model->findOne("propertyitems", $condition)) > 0 ? true : false;
    }
    public function registerPropertyItem($fields, $values){
        return $this->model->insertdata("propertyitems", $fields, $values);
    }
    public function retrieveAllPropertyItem($pageno, $limit){
        $data = $this->model->paginate("propertyitems", " 1 ORDER BY id ASC", $pageno, $limit);
        return $data;
    }  
    /** End of Property Items *****************************************************************************************************/
    
    /**Returns*********************************************************************************************************************/
    public function getInventoryReturnInfo($condition){
        return $this->model->findOne("returns", $condition);
    }
    public function checkIfInventoryReturnExists($condition){
        return count($this->model->findOne("returns", $condition)) > 0 ? true : false;
    }
    public function registerInventoryReturn($fields, $values){
        return $this->model->insertdata("returns", $fields, $values);
    }
    public function updateInventoryReturnDetails($query, $id){
        return $this->model->update('returns', $query, "WHERE id = $id");
    }    
    
    /***End of Returns**************************************************************************************************************/

    /**Gift*********************************************************************************************************************/
    public function getInventoryGiftInfo($condition){
        return $this->model->findOne("gift", $condition);
    }
    public function checkIfInventoryGiftExists($condition){
        return count($this->model->findOne("gift", $condition)) > 0 ? true : false;
    }
    public function registerInventoryGift($fields, $values){
        return $this->model->insertdata("gift", $fields, $values);
    }
    public function updateInventoryGiftDetails($query, $id){
        return $this->model->update('gift', $query, "WHERE id = $id");
    }    
    /***End of Gift**************************************************************************************************************/
    
    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function executeByQuerySelector($query){
        $data = $this->model->exec_query($query);
        return $data;
    }
}
?>