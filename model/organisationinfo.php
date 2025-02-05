<?php
class OrganisationInfo{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }

    public function getOrganisationInfo($condition){
        return $this->model->findOne("organisationinfo", $condition);
    }

    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function checkIfOrganisationExists($condition){
        return count($this->model->findOne("organisationinfo", $condition)) > 0 ? true : false;
    }

    public function registerOrganisation($fields, $values){
        return $this->model->insertdata("organisationinfo", $fields, $values);
    }

    public function retrieveOrganisations($id, $pageno, $limit){
        $res = $this->model->paginate("organisationinfo", "company_id = $id ORDER BY companyname ASC", $pageno, $limit);
        return $res;
    }

    public function getOrganisationFields($id, $location, $field){
        return $this->model->findOne("organisationinfo", "company_id = '$id'", "$field");
    }


    public function updateOrganisationImage($imageurl, $id){
        return $this->model->update('organisationinfo', "logo = '$imageurl'", "WHERE company_id = '$id'");
    }

    public function updateOrganisationDetails($query, $id){
        return $this->model->update('organisationinfo', "$query", "WHERE company_id = '$id'");
    }
    public function retrieveOrganisationByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("organisationinfo", $query, $pageno, $limit,$field);
        return $data;
    }      
    public function getOrganisations(){
        return $this->model->findAll("organisationinfo");
    }
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }

}
?>