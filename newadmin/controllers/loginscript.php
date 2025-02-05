<?php
//session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
//require_once '../model/smslog.php';
require_once '../../utils/common.php';
require_once '../../utils/sanitize.php';
date_default_timezone_set('Africa/Lagos');


//$admin = new Administration();
$user = new User();
//errorhandler("Operation: " . $_POST["operation"] );
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
if($email === "BAD"){
    $user->addUserActivity($email, "IP: " . $_POST["ip"] . " | Failed attemp to create user with bad email at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CREATE USER");
    exit(badRequest(204, "Bad email"));
}
$userinfo = $user->getUserInfo("email = '" . $email . "'");

$cdate = date('Y-m-d');
if($userinfo){
    /*check for non-working days
    $resultrejectdates = $admin->executeByQuerySelector("SELECT * FROM rejectdates WHERE rejectdate = '$cdate' AND location = " . $userinfo["location"]);
    if($resultrejectdates){
        if($userinfo["role"] !== "SUPERADMIN"){
            exit(badRequest(204, "The system is not available today!"));
        }
    }*/
    
    $phone = $user->getEscapedString(escape($_POST["email"]));
    //$pin = escape($_POST["pin"]);
    //$pin = hash("sha256",$pin);
    $location = (isset($_POST["location"]) && $_POST["location"] !== "") ? escape($_POST["location"]) : "location not set";
    //if(strlen($phone) === 11){
    
    session_start();
    $loginresult = $user->login($email, $_POST["upw"]);
    //exit("Email: " . $email . " upw: " . hash('sha256', escape($_POST["upw"])));
    //exit($loginresult);
    $lresult = json_decode($loginresult,true);
    //exit("code: " . $loginresult["code"]);
    if($lresult["code"] === 200){
        //echo $loginresult;
        $data = $lresult["data"];
        $sessiontoken = hash('sha256', $email);
        
        $profile = array(
            "firstname" => $data["firstname"],
            "lastname" => $data["lastname"],
            "othernames" => $data["othernames"],
            "role" => $data["role"],
            "referralcode" => $data["referralcode"],
            "sessiontoken" => $sessiontoken
            );
        $ressuccess = array("status" => true,"data"=>$profile,"code" => 200,"message" => "Successful");
        echo json_encode($ressuccess);
        //$fingerprint = (isset($_POST["fingerprint"]) && $_POST["fingerprint"] !== "") ? $_POST["fingerprint"] : "-";
        /*
        if($fingerprint === "-"){
            $_SESSION["phone"] = null;
            session_unset();
            session_destroy();
            $user->addUserActivity($phone, $phone . " | " . $location . " | Failed attemp to log in with bad fingerprint at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CREATE PIN");
            exit(badRequest(204, "Invalid credentials"));
        }*/
        //$res2 = $user->getUserInfo("email = '" . $email . "'");
        
        //$resp = array("status" => false,"code" => "204","message" => "Testing device setting","phone"=>$phone,"devices"=>$res2["devices"]);
        //exit(json_encode($resp));
        /*
        if(($res2 && $res2["devices"] === 1) || $res2 && $res2["devices"] === "1"){
            //$res = $user->getUserInfo("(fingerprint1 = '" . $fingerprint . "' AND devices = 1) OR (fingerprint2 = '" . $fingerprint . "' AND devices = 2)");
            $res = $user->getUserInfo("fingerprint1 = '" . $fingerprint . "'");
        }  elseif(($res2 && $res2["devices"] === 2) || $res2 && $res2["devices"] === "2"){
            $res = $user->getUserInfo("fingerprint2 = '" . $fingerprint . "'");
            //$res = $user->getUserInfo("(fingerprint1 = '" . $fingerprint . "' AND devices = 1) OR (fingerprint2 = '" . $fingerprint . "' AND devices = 2)");
        }
        if($res){
            $user->addUserActivity($phone, $phone . " | " . $location . " | logged in at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CREATE PIN");
            echo json_encode($loginresult);
        }else{
            $_SESSION["phone"] = null;
            session_unset();
            session_destroy();
            $res = array("status" => false,"code" => "204","message" => "This device is not activated for this account","phone"=>$phone);
            echo(json_encode($res));
        }*/
    }elseif($lresult["code"] === 300){
        $data = $lresult["data"];
        $sessiontoken = hash('sha256', $email);        
        $profile = array(
            "firstname" => $data["firstname"],
            "lastname" => $data["lastname"],
            "othernames" => $data["othernames"],
            "role" => $data["role"],
            "status" => "NOT VERIFIED",
            "referralcode" => $data["referralcode"],
            "sessiontoken" => $sessiontoken
            );
        $ressuccess = array("status" => true,"data"=>$profile,"code" => 200,"message" => "Successful");
        echo json_encode($ressuccess);        
        //$res = array("status" => true,"data"=>$lresult,"code" => "300","message" => "Unverified user");
        //echo json_encode($res);
        
    }else{
        $_SESSION["email"] = null;
        session_unset();
        session_destroy();
        
        $user->addUserActivity($email, $email . " | IP: " . $_POST["ip"] . " | Failed attempt to log in at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CREATE PIN");
        $res = array("status" => false,"code" => "204","message" => "Invalid credentials");
        echo json_encode($res);
        
    }
 
}else{
    $user->addUserActivity($email, $email . " | IP: " . $_POST["ip"] . " | Failed attempt to log in at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CREATE PIN");
    exit(badRequest(204, "Invalid credentials | email"));
}
?>
