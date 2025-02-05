<?php
class SMSLog{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }
    
    public function validate(){
        
    }
    

    public function getSMSInfo($condition){
        return $this->model->findOne("smslogs", $condition);
    }

    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }


    public function registerSMSLog($fields, $values){
        return $this->model->insertdata("smslogs", $fields, $values);
    }

    public function getSMSLogVerification($accountnumber){
        return $this->model->findOne("smslogs", "accountnumber = '$accountnumber' AND status LIKE 'VERIFICATION'");
    }   

    public function getSMSLogReset($accountnumber){
        return $this->model->findOne("smslogs", "accountnumber = '$accountnumber' AND status LIKE 'RESET'");
    }   
        
    public function updateSMSLogVerification($query, $id){
        return $this->model->update('smslogs', "$query", "WHERE id = '$id' AND status LIKE 'VERIFICATION'");
    }    
    public function updateSMSLogReset($query, $id){
        return $this->model->update('smslogs', "$query", "WHERE id = '$id' AND status LIKE 'RESET'");
    } 
    public function updateEmailLogReset($query, $id){
        return $this->model->update('smslogs', "$query", "WHERE id = '$id' AND status LIKE 'EMAILVERIFY'");
    } 

    /***** Email Verification ****************************************************/
    public function getEmailVerificationInfo($condition){
        return $this->model->findOne("verificationlogs", $condition);
    }
    public function registerEmailVerificationLog($fields, $values){
        return $this->model->insertdata("verificationlogs", $fields, $values);
    }
    public function getEmailLogVerification($accountnumber){
        return $this->model->findOne("verificationlogs", "accountnumber = '$accountnumber' AND status LIKE 'VERIFICATION'");
    }   
    public function updateVerificationLog($query, $id){
        return $this->model->update('verificationlogs', "$query", "WHERE id = '$id' AND status LIKE 'EMAILVERIFY'");
    }     
    /************* End Email Verification **************************************************************/
    
    public function retrieveByQuerySelector($query){
        return $this->model->exec_query($query);
    }
    
}