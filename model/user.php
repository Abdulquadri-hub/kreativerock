<?php
class User{

    public $model;

    public function __construct(){
        $this->model = new Model();
    }
    
    public function validate(){
        
    }
    
    public function login($email, $password){
        if(strlen($password) < 1){
            exit(badRequest(204, "Invalid Credentials"));
        }
        $password = escape($password);
        $hpassword = hash('sha256', $password);        
        //$hpassword = hash('ripemd128', "%9*" . $password . "7#");
        $user = $this->model->findOne('users', "email = '$email' AND upw = '$hpassword'");
        if ($user !== null) {
            //$organisation = new Organisation();
            //$organisationinfo = $organisation->getOrganisationInfo(" id = " . $user["organisation_id"]);
            //$administer = new Administration();
            //$locationinfo = $administer->getBranchInfo("id = '" . $res["location_id"] . "'");
            //$groups = $location->getGroups();
            $_SESSION["group"] = $user["class"];
            //$roles = new Roles();
            //$role = $roles->getRoleInfo("user_id = " . $user["id"]);
            $_SESSION["permissions"] = $user["permissions"];
            //$_SESSION["state"] = $locationinfo["state"];
            
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["elfuseremail"] = $email;
            $_SESSION["firstname"] = $user["firstname"];
            $_SESSION["lastname"] = $user["lastname"];
            //$_SESSION["location_id"] = $user["location_id"];
            $_SESSION["image"] = $user["imageurl"];
            $_SESSION["status"] = $user["status"];
            $_SESSION["address"] = $user["address"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["class"] = $user["class"];
            $_SESSION["phone"] = $user["phone"];
            //$_SESSION["supervisoremail"] = $user["supervisoremail"];
            //$_SESSION["supervisorphone"] = $user["supervisorphone"];
            /*$_SESSION["organisation_id"] = $user["organisation_id"];
            $_SESSION["organisationname"] = $organisationinfo["organisationname"];
            $_SESSION["organisationshortname"] = $organisationinfo["shortname"];
            $_SESSION["organisation_status"] = $organisationinfo["status"];*/
            //$_SESSION["logo"] = $organisationinfo["logo"];
            //$_SESSION["bvn"] = $organisationinfo["bvn"];
            

            if ($user["status"] === "DEACTIVATED") {
                $this->addUserActivity($user["id"], "Failed Attempt to login at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "LOGIN");
                return badRequest(205, "Invalid Credentials");
            }elseif($user["status"] === "NOT VERIFIED"){
                $this->addUserActivity($user["email"], "Successful login of unverified user at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "LOGIN");
                $this->updateUserDetails("online='YES'", $user["id"]); //set the online status
                return success($user, 300, 'Login Successful. User not Verified');
                
            } else {
                $this->addUserActivity($user["email"], "Successful Login at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "LOGIN");
                $this->updateUserDetails("online='YES'", $user["id"]); //set the online status
                return success($user, 200, 'Login Successful');
            }
        } else {
            $check = $this->model->findOne('users', "email = '$email'");
            if ($check !== null) {
                $this->addUserActivity($check["id"], "Failed Attempt to login at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "LOGIN");
            }
            return badRequest(204, "Invalid Credentials");
        }
    }    
    
    public function validateUser($email, $password){
        $hpassword = hash('sha256', $password);     
        return $this->model->findOne('users', "email = '$email' AND upw = '$hpassword'");
    }

    public function changePassword($email, $currentPassword, $newPassword){
        $hpassword = hash('sha256', $currentPassword);     
        $user = $this->model->findOne('users', "email = '$email' AND upw = '$hpassword'");

        if ($user !== null) {
             
            $newhash = hash('sha256', $newPassword);    
            $this->model->update('users', "upw = '$newhash'", "WHERE email = '$email'");
            return true; //success(null, 200, "Password changed successfully");
        } else {
            return false; //badRequest(400, "Invalid credentials.");
        }
    }

    public function initialChangePassword($email, $newPassword){
        $newhash = hash('sha256', $newPassword);    
        $change = $this->model->update('users', "upw = '$newhash', status='ACTIVE'", "WHERE email = '$email'");
        //$_SESSION["status"] = "ACTIVE";
        //return $change !== null ? success(null, 200, "Password Changed Successfully") : badRequest("Password change unsuccessful");
        return $change !== null ? 1 : 0; //success(null, 200, "Password Changed Successfully") : badRequest("Password change unsuccessful");
    }

    public function resetPassword($email, $password){
    }    
    
    public function getUserInfo($condition){
        return $this->model->findOne("users", $condition);
    }

    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function checkIfUserExists($id){
        return count($this->model->findOne("users", "id = '$id'")) > 0 ? true : false;
    }

    public function registerUser($fields, $values){
        //echo $values;
        return $this->model->insertdata("users", $fields, $values);
    }
    public function passwordReset($fields, $values){
        return $this->model->insertdata("preset", $fields, $values);
    }

    public function retrieveUsersByLocation($location_id, $pageno, $limit){
        $users = $this->model->paginate("users", " location_id = $location_id ORDER BY lastname, firstname DESC", $pageno, $limit);
        return $users;
    }
    public function retrieveUsersByOrganisation($organisation_id, $pageno, $limit){
        $users = $this->model->paginate("users", " organisation_id = $organisation_id ORDER BY lastname, firstname DESC", $pageno, $limit);
        return $users;
    }
    public function retrieveUsersByClass($class, $pageno, $limit){
        $users = $this->model->paginate("users", " class LIKE $class ORDER BY lastname, firstname DESC", $pageno, $limit);
        return $users;
    }

    public function getUserFields($accountnumber, $location, $field){
        return $this->model->findOne("users", "accountnumber = '$accountnumber' AND location = '$location'", "$field");
    }   
    
    public function addUserActivity($id, $description, $date, $status){
        return $this->model->insertdata("user_activity", "username, description, currenttime, status", "'$id','$description','$date','$status'");
    }

    public function updateUserImage($imageurl, $id){
        return $this->model->update('users', "imageurl = '$imageurl'", "WHERE id = '$id'");
    }

    public function updateUserDetails($query, $id){
        return $this->model->update('users', "$query", "WHERE id = '$id'");
    }    

    public function getRecentActivities($userid)
    {
        return $this->model->findAllWhere("users_activity", "user_id = $userid ORDER BY created_at DESC LIMIT 5");
    }

    public function getActivityList($userid, $limit)
    {
        $data = array("count" => "", "data" => "", "limit" => "");
        $total = $this->model->getCount("users_activity", "user_id =$userid");
        $result = $this->model->findAllWhere("users_activity", "user_id =$userid ORDER BY created_at DESC LIMIT $limit");

        $data["count"] = $total;
        $data["limit"] = $limit;
        $data["data"] = $result;
        return $data;
    }

    public function getUserActivityInRange($userid, $limit, $startdate, $enddate)
    {
        $data = array("count" => "", "data" => "", "limit" => "");
        $total = $this->model->getCount("users_activity", "user_id = '$userid' AND currenttime BETWEEN '$startdate' AND '$enddate'");
        $result = $this->model->findAllWhere("users_activity", "user_id = '$userid' AND currenttime BETWEEN '$startdate' AND '$enddate' ORDER BY created_at DESC LIMIT $limit", "*, (SELECT CONCAT(lastname, ' ', firstname) FROM users WHERE id = members_activity.user_id) as username");

        $data["count"] = $total;
        $data["limit"] = $limit;
        $data["data"] = $result;
        return $data;
    }    
    public function getEscapedString($param){
        return $this->model->escapeString($param);
    }    
    public function retrieveByQuerySelector($query){
        $res = $this->model->exec_query($query);
        return $res;
    }

    public function getUnits(){
        //
    }
    
}