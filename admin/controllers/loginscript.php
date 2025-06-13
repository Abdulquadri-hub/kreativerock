<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$user = new User();

$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
if($email === "BAD"){
    $user->addUserActivity($email, "IP: " . $_POST["ip"] . " | Failed attemp to create user with bad email at " . date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "CREATE USER");
    exit(badRequest(204, "Bad email"));
}

$userinfo = $user->getUserInfo("email = '" . $email . "'");
$cdate = date('Y-m-d');

if($userinfo){
    
    $phone = $user->getEscapedString(escape($_POST["email"]));
    $location = (isset($_POST["location"]) && $_POST["location"] !== "") ? escape($_POST["location"]) : "location not set";
    
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
            "status" => $data["status"],
            "referralcode" => $data["referralcode"],
            "sessiontoken" => $sessiontoken
            );
        $ressuccess = array("status" => true,"data"=>$profile,"code" => 200,"message" => "Successful");
        echo json_encode($ressuccess);

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
