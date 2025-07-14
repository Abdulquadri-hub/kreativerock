<?php
class User{

    public $model;
    private $db;
    protected $roles_and_permission;
    private $usersTable = "users";
    private $emailTemplate; 

    public function __construct(){
        $this->model = new Model();
        $this->db = new dbFunctions();
        $this->roles_and_permission = new RolesAndPermissions();
        $this->emailTemplate = new Email_Template(); 
    }
    
    public function login($email, $password){
        if(strlen($password) < 1){
            exit(badRequest(204, "Invalid Credentials"));
        }
        $password = escape($password);
        $hpassword = hash('sha256', $password);        

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
                $this->updateUserDetails("online='YES'", $user["id"]); 
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

    public function getUserIdByEmail($email){
        $email = $this->db->escape($email);
        $user = $this->db->find($this->usersTable, "email = '$email'");
        return $user['id'];
    }

    public function getUserByEmail($email){
        $email = $this->db->escape($email);
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(!empty($user)){
            return $user;
        }
        return [];
    }

    public function create($userData)
    {
        $validation = $this->validate($userData);
        if ($validation !== true) {
            return $validation;
        }
        
        if (isset($userData['upw'])) {
            $userData['upw'] = hash('sha256', $userData['upw']);
        }
        
        $userData['referralcode'] = hash('sha256', $userData['email']);
        
        $userData['status'] = 'NOT VERIFIED';
        
        if (!isset($userData['class']) || empty($userData['class'])) {
            $userData['class'] = 'USER';
        }
        
        $userData['tlog'] = (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : '') . ' ' . date('Y-m-d H:i:s');
        
        if (!isset($userData['role']) || empty($userData['role'])) {
            $userData['role'] = $userData['class'];
        }
        
        if (!isset($userData['dateofbirth']) || empty($userData['dateofbirth'])) {
            $userData['dateofbirth'] = '2000-01-01';
        }
        
        $userData['online'] = 'NO';
        
        $userId = $this->db->insert($this->usersTable, $userData);
        
        if ($userId) {
            $userRole = $this->roles_and_permission->getRoleByName($userData['role']);
            $this->roles_and_permission->assignRole($userId, $userRole['id']);
    
            $verificationCode = $this->generateVerificationCode();
            $this->saveVerificationCode($userData['email'], $verificationCode);
            
            // $message = "Your account verification code is: " . $verificationCode;
            // $this->sendVerificationLink($userData['email'], $message, $verificationCode);

            $userName = $userData['firstname'] . ' ' . $userData['lastname'];
            $this->sendVerificationLink($userData['email'], $verificationCode, $userName);
            
            // Generate API key
            $apiKeyManager = new ApiKeyManager();
            $apiKeyManager->generateApiKey($userId);
            
            return true;
        }
        
        return false;
    }

    public function resetPassword($email, $password)
    {
        $email = $this->db->escape($email);
        $newhash = hash('sha256', $password);
        
        $resetToken = bin2hex(random_bytes(32));
        $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $resetData = [
            'email' => $email,
            'token' => $resetToken,
            'expires_at' => $expiryTime,
            'status' => 'PENDING'
        ];
        
        $inserted = $this->db->insert('password_resets', $resetData);
        
        if ($inserted) {
            // Send password reset email
            $emailEncoded = base64_encode($email);
            $tokenEncoded = base64_encode($resetToken);
            
            $local = "http://localhost/kreativerock/admin/controllers/resetpassword.php?email=$emailEncoded&token=$tokenEncoded";
            $live = "https://comeandsee.com.ng/kreativerock/admin/controllers/resetpassword.php?email=$emailEncoded&token=$tokenEncoded";
            
            $link = $local; // Change to $live in production
            $tmessage = "</b> Click the link below to reset your password: <br /><br /> <span style='padding:7px;background-color:#1E90FF;    color:white;'><a href='" . $link . "' style='text-decoration:none;color:white;'>Reset Password</a></span>";
            $tmessage .= "<br /><br />This link will expire in 24 hours.";
            
            $message = "<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
            $message .= "<br /> " . $tmessage . "<br />";
            $message .= "</div>";
            
            $payload = [
                "email" => $email,
                "message" => $message,
                "subject" => "Password Reset Request"
            ];
            
            $payload = json_encode($payload);
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://comeandsee.com.ng/mailer/sendmailtoElfrique.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "cache-control: no-cache",
                    "content-type: application/json"
                ],
            ]);
            
            $result = curl_exec($curl);
            curl_close($curl);
            
            return true;
        }
        
        return false;
    }

    public function completePasswordReset($email, $token, $newPassword)
    {
        $email = $this->db->escape($email);
        $token = $this->db->escape($token);
        
        // Find the reset token
        $resetRecord = $this->db->find('password_resets', "email = '$email' AND token = '$token' AND status = 'PENDING' AND expires_at > NOW()");
        
        if ($resetRecord) {
            // Update the password
            $newHash = hash('sha256', $newPassword);
            $updated = $this->db->update($this->usersTable, [
                'upw' => $newHash
            ], "email = '$email'");
            
            if ($updated) {
                // Mark the token as used
                $this->db->update('password_resets', [
                    'status' => 'USED'
                ], "email = '$email' AND token = '$token'");
                
                return true;
            }
        }
        
        return false;
    }

    // publicf

    public function updateUserProfileDetails($userId, $data) {
        if (!$userId || !is_array($data) || empty($data)) {
            return false;
        }
        
        $protectedFields = ['id', 'email', 'referralcode', 'status', 'created_at'];
        foreach ($protectedFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        
        $cleanData = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            
            if ($key === 'dateofbirth') {
                $cleanData[$key] = $value;
            } else {
                $cleanData[$key] = htmlentities($value, ENT_QUOTES);
            }
        }
        
        if (empty($cleanData)) {
            return false;
        }
        
        // Update the user record
        return $this->db->update($this->usersTable, $cleanData, "id = " . (int)$userId);
    } 

    public function checkEmailAlreadyExists($email){
        $email = $this->db->escape($email);
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(empty($user)){
            return false;
        }

        return true;
    }

    public function getUser($userId)
    {
        $sql = "
            SELECT 
                u.*,
                r.id as role_id,
                r.name as role_name,
                r.description as role_description,
                p.id as permission_id,
                p.name as permission_name,
                p.description as permission_description
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN role_permissions rp ON r.id = rp.role_id
            LEFT JOIN permissions p ON rp.permission_id = p.id
            WHERE u.id = ?
            ORDER BY r.id, p.id
        ";
    
        $result = $this->db->query($sql, [$userId]);
    
        if (!$result) {
            return null;
        }
    
        $userData = null;
        $roles = [];
        $permissions = [];
        $processedRoles = [];
        $processedPermissions = [];
    
        foreach ($result as $row) {
            if (!$userData) {
                $userData = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
                    'status' => $row['status'],
                    'address' => $row['address'],
                    'class' => $row['class'],
                    'role' => $row['role'],
                    'dateofbirth' => $row['dateofbirth'],
                    'referralcode' => $row['referralcode']
                ];
            }
    
            if ($row['role_id'] && !in_array($row['role_id'], $processedRoles)) {
                $roles[] = [
                    'id' => $row['role_id'],
                    'name' => $row['role_name'],
                    'description' => $row['role_description']
                ];
                $processedRoles[] = $row['role_id'];
            }
    
            if ($row['permission_id'] && !in_array($row['permission_id'], $processedPermissions)) {
                $permissions[] = [
                    'id' => $row['permission_id'],
                    'name' => $row['permission_name'],
                    'description' => $row['permission_description']
                ];
                $processedPermissions[] = $row['permission_id'];
            }
        }
    
        if ($userData) {
            $userData['roles'] = $roles;
            $userData['permissions'] = $permissions;
    
            $smsSql = "SELECT * FROM transactions WHERE user = ?";
            $transactions = $this->db->query($smsSql, [$userData['email']]);
    
            $userData['transactions'] = $transactions ?: [];
        }
    
        return $userData;
    }

    
    public function getAllUsers() {
        $sql = "
            SELECT 
                u.*,
                r.id as role_id,
                r.name as role_name,
                r.description as role_description,
                p.id as permission_id,
                p.name as permission_name,
                p.description as permission_description
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN role_permissions rp ON r.id = rp.role_id
            LEFT JOIN permissions p ON rp.permission_id = p.id
            ORDER BY u.id, r.id, p.id
        ";
        
        $result = $this->db->query($sql);
        
        if(!$result) {
            return [];
        }
        
        $users = [];
        $processedUsers = [];
        
        foreach($result as $row) {
            $userId = $row['id'];
            
            // Initialize user if not processed
            if(!isset($processedUsers[$userId])) {
                $processedUsers[$userId] = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
                    'status' => $row['status'],
                    'address' => $row['address'],
                    'class' => $row['class'],
                    'role' => $row['role'],
                    'dateofbirth' => $row['dateofbirth'],
                    'referralcode' => $row['referralcode'],
                    'roles' => [],
                    'permissions' => [],
                    'processedRoles' => [],
                    'processedPermissions' => []
                ];
            }
            
            // Add unique roles
            if($row['role_id'] && !in_array($row['role_id'], $processedUsers[$userId]['processedRoles'])) {
                $processedUsers[$userId]['roles'][] = [
                    'id' => $row['role_id'],
                    'name' => $row['role_name'],
                    'description' => $row['role_description']
                ];
                $processedUsers[$userId]['processedRoles'][] = $row['role_id'];
            }
            
            // Add unique permissions
            if($row['permission_id'] && !in_array($row['permission_id'], $processedUsers[$userId]['processedPermissions'])) {
                $processedUsers[$userId]['permissions'][] = [
                    'id' => $row['permission_id'],
                    'name' => $row['permission_name'],
                    'description' => $row['permission_description']
                ];
                $processedUsers[$userId]['processedPermissions'][] = $row['permission_id'];
            }
        }
        
        // Clean up helper arrays and return indexed array
        foreach($processedUsers as &$user) {
            unset($user['processedRoles']);
            unset($user['processedPermissions']);
        }
        
        return array_values($processedUsers);
    }

    //  email verifications

    public function emailVerification($email, $verificationCode){
        $email = $this->db->escape($email);
        $verificationCode = $this->db->escape($verificationCode);
        
        $result = $this->db->find('email_verifications', "email = '$email' AND verificationcode = '$verificationCode' AND status = 'EMAILVERIFY'");
        
        if ($result !== null) {
            $this->db->update($this->usersTable, [
                "status" => 'ACTIVE'
            ], "email = '$email'");

            $this->db->update('email_verifications', [
                "status" => "VERIFIED"
            ], "email = '$email' AND verificationcode = '$verificationCode'");

            return true;
        }
        
        return false;
    }

    public function sendVerificationLink_old($email, $message, $verificationcode){
        $emailencoded = base64_encode($email);
        $vercode = base64_encode($verificationcode);

        $local = "http://localhost/kreativerock/admin/controllers/verifyuser.php?email=$emailencoded&vercode=$vercode";
        $live = "https://comeandsee.com.ng/kreativerock/admin/controllers/verifyuser.php?email=$emailencoded&vercode=$vercode";
        
        $link = $live; // Change to $local for local testing
        $tmessage = "</b> Click the link below to verify your account: <br /><br /> <span style='padding:7px;background-color:#1E90FF;color:white;'><a href='" . $link . "' style='text-decoration:none;color:white;'>Click this link</a></span>";
        
        $message = "<div style='text-align:left;font-size=12px;color=#000000;font-family=serif'>";
        $message .= "<br /> " . $tmessage . "<br />";
        $message .= "</div>";
        
        $payload = array(
            "email" => $email,
            "message" => $message,
            "subject" => "KreativeRock Email Verification"
        );
            
        $payload = json_encode($payload);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://comeandsee.com.ng/mailer/sendmailtokreativerock.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
              "cache-control: no-cache",
              "content-type: application/json"
            ),
        ));
        
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    
    public function generateVerificationCode() {
        return mt_rand(1000000000, 9999999999);
    }
    
    public function saveVerificationCode($email, $verificationCode) {
        $email = $this->db->escape($email);
        $verificationCode = $this->db->escape($verificationCode);
        $status = "EMAILVERIFY";
        $tlog = date("D, d M Y H:i:s");
        
        return $this->db->insert('email_verifications', [
            'date' => date('Y-m-d H:i:s'),
            'email' => $email,
            'status' => $status,
            'verificationcode' => $verificationCode,
            'tlog' => $tlog
        ]);
    }

    public function getVerificationLog($email){
        return $this->db->find("email_verifications", "email = '$email' AND status LIKE 'EMAILVERIFY'", "id DESC");
    }

    public function updateVerificationLog($verificationLogId){

        return $this->db->update('email_verifications', [
            'status' => "EMAILVERIFIED"
        ], "id = '$verificationLogId' AND status LIKE 'EMAILVERIFY'");     
    }

    public function sendWelcomeEmail($email, $userName, $dashboardLink, $profileLink) {
        // Use the email template for welcome message
        $htmlMessage = $this->emailTemplate->getWelcomeEmailTemplate($userName, $dashboardLink, $profileLink);
        
        $payload = array(
            "email" => $email,
            "message" => $htmlMessage,
            "subject" => "Welcome to KreativeRock - Your Account is Active!"
        );
            
        $payload = json_encode($payload);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://comeandsee.com.ng/mailer/sendmailtokreativerock.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
              "cache-control: no-cache",
              "content-type: application/json"
            ),
        ));
        
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public function sendVerificationLink($email, $verificationCode, $userName = ''){
        $emailencoded = base64_encode($email);
        $vercode = base64_encode($verificationCode);

        $local = "http://localhost/kreativerock/admin/controllers/verifyuser.php?email=$emailencoded&vercode=$vercode";
        $live = "https://comeandsee.com.ng/kreativerock/admin/controllers/verifyuser.php?email=$emailencoded&vercode=$vercode";
        
        $verificationLink = $live; // Change to $local for local testing
        
        // Use the email template for verification
        $htmlMessage = $this->emailTemplate->getVerificationEmailTemplate($userName, $verificationCode, $verificationLink);
        
        $payload = array(
            "email" => $email,
            "message" => $htmlMessage,
            "subject" => "KreativeRock - Verify Your Email Address"
        );
            
        $payload = json_encode($payload);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://comeandsee.com.ng/mailer/sendmailtokreativerock.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
              "cache-control: no-cache",
              "content-type: application/json"
            ),
        ));
        
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

   /** Email verification ends */

    public function validate($data)
    {
        $requiredFields = [
            'firstname',
            'lastname',
            // 'othernames',
            'address',
            'email',
            'phone',
            'upw'
        ];
    
        $errors = [];
    
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }
    
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email format is invalid.';
        }elseif($this->checkEmailAlreadyExists($data['email'])){
            $errors['email'] = "Email already taken";
        }
    
        if (!empty($data['phone']) && !preg_match('/^\+?[0-9]{7,15}$/', $data['phone'])) {
            $errors['phone'] = 'Phone number format is invalid.';
        }
    
        return empty($errors) ? true : $errors;
    }

}
