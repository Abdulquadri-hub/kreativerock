<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

$apiKeyManger = new ApiKeyManager();
$rolesAndPermissions = new RolesAndPermissions();

$filename1 = "";
if(isset($_FILES["userphotoname"]["name"]) && $_FILES["userphotoname"]["name"] !== null && $_FILES["userphotoname"]["name"] !== "-"){
	$filenamearray = explode(".",$_FILES["userphotoname"]["name"]); 
	
	if(isset($_POST['identificationtype'])){
        $identificationType = $_POST["identificationtype"];
        if($identificationType === "National ID"){
           $filename1 = $filenamearray[0] . mt_rand(10,111111111) . ".national_id." . end($filenamearray);
        }
		elseif($identificationType === "INTERNATIONAL PASSPORT"){
            $filename1 = $filenamearray[0] . mt_rand(10,111111111) . ".international_passport." . end($filenamearray);
        }
		elseif($identificationType === "DRIVERS LICENSE"){
            $filename1 = $filenamearray[0] . mt_rand(10,111111111) . ".driver_license." . end($filenamearray);
        }
		else {
            $filename1 = $filenamearray[0] . mt_rand(10,111111111) . "." . end($filenamearray);
        }
    }
	else {
        $filename1 = $filenamearray[0] . mt_rand(10,111111111) . "." . end($filenamearray);
    }
}
else { 
	$filename1 = "-";
}

$returnvalue = '';
if(isset($_POST["photofilename"]) && $_POST["photofilename"] !== null && $_POST["photofilename"] !== "-"){	
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES["userphotoname"]["name"]);
	$returnvalue .= '' . $_FILES["userphotoname"]["name"];
	$extension = end($temp);
	if (
	    (($_FILES["userphotoname"]["type"] === "image/gif")
	    || ($_FILES["userphotoname"]["type"] === "image/png") 
	    || ($_FILES["userphotoname"]["type"] === "image/jpeg") 
	    || ($_FILES["userphotoname"]["type"] === "image/jpg") 
	    || ($_FILES["userphotoname"]["type"] === "image/pjpeg"))
	    && in_array($extension, $allowedExts)
	   ){
	
		if ($_FILES["userphotoname"]["error"] > 0){
			$returnvalue .= ' | error saving photo'; 
			exit(message(500, "false", $returnvalue));
		}
		else{
		    $user = new User();
            $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD"; 
            $res = $user->getUserInfo("email = '" . $email . "'");
            
            $rootFolder = $_SERVER['DOCUMENT_ROOT'];
            $target = $rootFolder ."/kreativerock/images/kyc/";
            
            if($res){
                $oldImageUrl = $res["imageurl"];
                if(file_exists($target . $oldImageUrl))
                {
                    unlink($target . $oldImageUrl);
                }
            }
		    
		    if(isset($_POST["identificationtype"]) &&  $_POST["identificationtype"]  !== ""){
		         $identificationType = $_POST["identificationtype"];
       
                if($identificationType === "National ID"){
                    move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);
                }
				elseif($identificationType === "INTERNATIONAL PASSPORT"){
                    move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);
                }
				elseif($identificationType === "DRIVERS LICENSE"){
                    move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);
                }
				else {
                    move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);
                }
		    }
			else {
				move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target. $filename1);
		    }
		    $returnvalue .= " Upload Successful";
		}
	}
	else{
            $returnvalue .= ' | Invalid file type';
            exit(message(500, "false", $returnvalue));
	}
}
     
$user = new User();
$email = isset($_POST["email"]) &&  filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
$res = $user->getUserInfo("email = '" . $email . "'");
$becomeamerchant = (isset($_POST["becomeamerchant"]) && $_POST["becomeamerchant"] !== "") ? htmlentities($_POST["becomeamerchant"],ENT_QUOTES) : "NO";
$becomeamerchant = $becomeamerchant === "YES" ? "MERCHANT" : "USER";

if($res){
    $id = $res["id"];

    $phone = ((isset($_POST["phone"]) && is_numeric($_POST["phone"]) && strlen($_POST["phone"]) === 11) ? $_POST["phone"] : $res["phone"]);

    $updateData = [
        'lastname' => $_REQUEST["lastname"] ?? $res["lastname"],
        'firstname' => $_REQUEST["firstname"] ?? $res["firstname"],
        'othernames' => $_REQUEST["othernames"] ?? $res["othernames"],
        'dateofbirth' => $_REQUEST["dateofbirth"] ?? $res["dateofbirth"],
        'identificationtype' => $_REQUEST["identificationtype"] ?? $res["identificationtype"],
        'imageurl' => ($filename1 !== "" && $filename1 !== "-") ? $filename1 : $res["imageurl"],
        'address' => $_REQUEST["address"] ?? $res["address"],
        'phone' => ((isset($_POST["phone"]) && is_numeric($_POST["phone"]) && strlen($_POST["phone"]) === 11) ? $_POST["phone"] : $res["phone"]),
        'class' => $_REQUEST["class"] ?? $res["class"],
        'organisation' => $_REQUEST["organisation"] ?? $res["organisation"],
        'industry' => $_REQUEST["industry"] ?? isset($res["industry"])? $res["industry"] : "",
        'positioninthecompany' => $_REQUEST["positioninthecompany"] ?? $res["positioninthecompany"],
        'currency' => $_REQUEST["currency"] ?? $res["currency"],
        'timezone' => $_REQUEST["timezone"] ?? $res["timezone"],
        'country' => $_REQUEST["country"] ?? $res["country"],
    ];
    
    $result = $user->updateUserProfileDetails($id, $updateData);
    if ($result) {
        echo success($result, 200, "User records updated successfully", "Successful");
    } else {
        echo badRequest(204, "Not successful");
    }
	
} else {
    $user = new User();

    $userData = [
        'email' => isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD",
        'lastname' => isset($_REQUEST["lastname"]) && $_REQUEST["lastname"] !== '' ? htmlentities($_REQUEST["lastname"], ENT_QUOTES) : '-',
        'firstname' => isset($_REQUEST["firstname"]) && $_REQUEST["firstname"] !== '' ? htmlentities($_REQUEST["firstname"], ENT_QUOTES) : '-',
        'othernames' => isset($_REQUEST["othernames"]) && $_REQUEST["othernames"] !== '' ? htmlentities($_REQUEST["othernames"], ENT_QUOTES) : '-',
        'imageurl' => $filename1,
        'address' => isset($_REQUEST["address"]) && $_REQUEST["address"] !== '' ? htmlentities($_REQUEST["address"], ENT_QUOTES) : '-',
        'phone' => isset($_POST["phone"]) && is_numeric($_POST["phone"]) && strlen($_POST["phone"]) >= 10 ? $_POST["phone"] : "BAD",
        'upw' => isset($_POST["upw"]) && $_POST["upw"] !== "" ? $_POST["upw"] : "BAD",
        'class' => $becomeamerchant,
        'permissions' => isset($_REQUEST["permissions"]) && $_REQUEST["permissions"] !== '' ? htmlentities($_REQUEST["permissions"], ENT_QUOTES) : '-',
        'dateofbirth' => isset($_REQUEST["dateofbirth"]) && $_REQUEST["dateofbirth"] !== '' ? $_REQUEST["dateofbirth"] : '2000-01-01',
        'role' => $becomeamerchant
    ];

    if ($userData['upw'] === "BAD" || $userData['email'] === "BAD" || $userData['phone'] === "BAD") {
        exit(badRequest(204, "Bad credentials"));
    }
    
    $result = $user->create($userData);
    
    if (is_numeric($result)) {  
        $data = $user->retrieveByQuerySelector("SELECT * FROM users WHERE email = '" . $user->getEscapedString($userData['email']) . "' LIMIT 1");
        $data = $data[0];
        
        echo success($data, 200, "Registration successful. Please check your email for verification link.", "Successful");
    } else if (is_array($result)) { 
        echo badRequest(204, "Registration not successful: " . implode(", ", $result));
    } else {
        echo badRequest(204, "Registration not successful");
    }
}

?>

