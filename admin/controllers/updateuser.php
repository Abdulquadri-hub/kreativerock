<?php
session_start();
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
     exit(badRequest(204,'Invalid session data. Proceed to login'));
}

//$email = (isset($_POST["email"]) && $_POST["email"]) ? $_POST["email"] : "BAD";
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
$user = new User();

if($email === "BAD"){
    exit(badRequest(204,'Bad user data'));
}else{
    if($_SESSION['role'] === "SUPERADMIN" || $_SESSION['class'] === "SUPERADMIN"){
        $resuser = $user->getUserInfo("email = '" . $email . "'");
        if(!$resuser || $resuser === null){
            exit(badRequest(204,'We could not find this user'));
        }
    
        $role = (isset($_POST["role"]) && $_POST["role"] !== "") ? htmlentities($_POST["role"],ENT_QUOTES) : $resuser["role"];
        $lastname = (isset($_POST["lastname"]) && $_POST["lastname"] !== "") ? htmlentities($_POST["lastname"],ENT_QUOTES) : $resuser["lastname"];
        $firstname = (isset($_POST["firstname"]) && $_POST["firstname"] !== "") ? htmlentities($_POST["firstname"],ENT_QUOTES) : $resuser["firstname"];
        $othernames = (isset($_POST["othernames"]) && $_POST["othernames"] !== "") ? htmlentities($_POST["othernames"],ENT_QUOTES) : $resuser["othernames"];
        $phone = (isset($_POST["phone"]) && $_POST["phone"] !== "") ? htmlentities($_POST["phone"],ENT_QUOTES) : $resuser["phone"];
        $address = (isset($_POST["address"]) && $_POST["address"] !== "") ? htmlentities($_POST["address"],ENT_QUOTES) : $resuser["address"];
        $dateofbirth = (isset($_POST["dateofbirth"]) && $_POST["dateofbirth"] !== "") ? htmlentities($_POST["dateofbirth"],ENT_QUOTES) : $resuser["dateofbirth"];
        
        $result = $user->retrieveByQuerySelector("UPDATE users SET role = '$role',lastname='$lastname',firstname='$firstname',othernames='$othernames',phone='$phone',address='$address',dateofbirth='$dateofbirth' WHERE (role NOT LIKE 'SUPERADMIN' OR class NOT LIKE 'SUPERADMIN') AND id = " . $resuser["id"]);
    }else{
        $resuser = $user->getUserInfo("email = '" . $_SESSION['elfuseremail'] . "'");
        if(!$resuser || $resuser === null){
            exit(badRequest(204,'We could not find this user'));
        }
    
        //$role = (isset($_POST["role"]) && $_POST["role"] !== "") ? htmlentities($_POST["role"],ENT_QUOTES) : $resuser["role"];
        $lastname = (isset($_POST["lastname"]) && $_POST["lastname"] !== "") ? htmlentities($_POST["lastname"],ENT_QUOTES) : $resuser["lastname"];
        $firstname = (isset($_POST["firstname"]) && $_POST["firstname"] !== "") ? htmlentities($_POST["firstname"],ENT_QUOTES) : $resuser["firstname"];
        $othernames = (isset($_POST["othernames"]) && $_POST["othernames"] !== "") ? htmlentities($_POST["othernames"],ENT_QUOTES) : $resuser["othernames"];
        $phone = (isset($_POST["phone"]) && $_POST["phone"] !== "") ? htmlentities($_POST["phone"],ENT_QUOTES) : $resuser["phone"];
        $address = (isset($_POST["address"]) && $_POST["address"] !== "") ? htmlentities($_POST["address"],ENT_QUOTES) : $resuser["address"];
        $dateofbirth = (isset($_POST["dateofbirth"]) && $_POST["dateofbirth"] !== "") ? htmlentities($_POST["dateofbirth"],ENT_QUOTES) : $resuser["dateofbirth"];
        
        $result = $user->retrieveByQuerySelector("UPDATE users SET lastname='$lastname',firstname='$firstname',othernames='$othernames',phone='$phone',address='$address',dateofbirth='$dateofbirth' WHERE id = " . $resuser["id"]);
        
    }
    if($result){
        echo success($_SESSION["selecteduser"],200, "Successful","Successful");
    }else{
        exit(badRequest(204,'Not successful'));
    }
}
	
?>