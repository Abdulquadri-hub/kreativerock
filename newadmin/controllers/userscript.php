<?php
//ini_set('display_errors', 1);
//exit("Started savings");
session_start();


require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/user.php';
require_once '../../utils/sanitize.php';

$filename1 = "";
if($_FILES["userphotoname"]["name"] !== "" && $_FILES["userphotoname"]["name"] !== null && $_FILES["userphotoname"]["name"] !== "-"){
	$filenamearray = explode(".",$_FILES["userphotoname"]["name"]); 
	
	if(isset($_POST['identificationtype']))
    {
        
        $identificationType = $_POST["identificationtype"];
        if($identificationType === "National ID")
        {
        
           $filename1 = $filenamearray[0] . mt_rand(10,111111111) . ".national_id." . end($filenamearray);
                        
        }else
        if($identificationType === "INTERNATIONAL PASSPORT")
        {
            
            $filename1 = $filenamearray[0] . mt_rand(10,111111111) . ".international_passport." . end($filenamearray);
                        
        }else
        if($identificationType === "DRIVERS LICENSE")
        {
            
            $filename1 = $filenamearray[0] . mt_rand(10,111111111) . ".driver_license." . end($filenamearray);
                        
        }else {
            
            $filename1 = $filenamearray[0] . mt_rand(10,111111111) . "." . end($filenamearray);
        }
        
    }else {
        
        $filename1 = $filenamearray[0] . mt_rand(10,111111111) . "." . end($filenamearray);
    }
    
}else { $filename1 = "-"; }

	$returnvalue = '';
    if($_POST["photofilename"] !== null && $_POST["photofilename"] !== "-")
    {	
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
			}else{
			    
			    $user = new User();
                $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD"; 
                $res = $user->getUserInfo("email = '" . $email . "'");
                
                $rootFolder = $_SERVER['DOCUMENT_ROOT'];
                $target = $rootFolder ."/kreativerock/images/kyc/";
                
                if($res)
                {
                    $oldImageUrl = $res["imageurl"];
                    if(file_exists($target . $oldImageUrl))
                    {
                        unlink($target . $oldImageUrl);
                    }
                }
			    
			    if(isset($_POST["identificationtype"]) &&  $_POST["identificationtype"]  !== "")
			    {
			         $identificationType = $_POST["identificationtype"];
           
                    if($identificationType === "National ID")
                    {
                        move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);

                    }elseif($identificationType === "INTERNATIONAL PASSPORT")
                    {
            
                        move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);
                
                    }elseif($identificationType === "DRIVERS LICENSE")
                    {
                        move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);

                    }else {
                        
                        move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target . $filename1);
                    }
                    
			    }else {
			        
					move_uploaded_file($_FILES["userphotoname"]["tmp_name"], $target. $filename1);
			    }
			    
			    $returnvalue .= " Upload Successful";
			 //   echo message(200, "true", $returnvalue);
    		}
    		
		}else{
             $returnvalue .= ' | Invalid file type';
             exit(message(500, "false", $returnvalue));
		}
		
      }
     

$user = new User();
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
$res = $user->getUserInfo("email = '" . $email . "'");
$becomeamerchant = (isset($_POST["becomeamerchant"]) && $_POST["becomeamerchant"] !== "") ? htmlentities($_POST["becomeamerchant"],ENT_QUOTES) : "NO";
$becomeamerchant = $becomeamerchant === "YES" ? "MERCHANT" : "USER";
//exit("Session data: "  . $_SESSION["elfuseremail"]);

//exit("Email Session: " . $_SESSION["email"]);
//if($res && $_POST["email"] === $_SESSION["email"]){
if($res){
    $id = $res["id"];

    $phone = ((isset($_POST["phone"]) && is_numeric($_POST["phone"]) && strlen($_POST["phone"]) === 11) ? $_POST["phone"] : $res["phone"]);
    
	$query = "lastname = '" . ((isset($_REQUEST["lastname"]) && $_REQUEST["lastname"] !== '') ? htmlentities($_REQUEST["lastname"],ENT_QUOTES) : $res["lastname"]) . "',
	firstname = '" . ((isset($_REQUEST["firstname"]) && $_REQUEST["firstname"] !== '') ? htmlentities($_REQUEST["firstname"],ENT_QUOTES) : $res["firstname"]) . "',
	othernames = '" . ((isset($_REQUEST["othernames"]) && $_REQUEST["othernames"] !== '') ? htmlentities($_REQUEST["othernames"],ENT_QUOTES) : $res["othernames"]) . "',
	dateofbirth = '" . ((isset($_REQUEST["dateofbirth"]) && $_REQUEST["dateofbirth"] !== '') ? $_REQUEST["dateofbirth"] : $res["dateofbirth"]) . "',
	identificationtype = '" . ((isset($_REQUEST["identificationtype"]) && $_REQUEST["identificationtype"] !== '') ? $_REQUEST["identificationtype"] : $res["identificationtype"]) . "',
	imageurl = '" . (($filename1 !== "" && $filename1 !== "-") ? $filename1 : $res["imageurl"]) . "',
	address = '" . ((isset($_REQUEST["address"]) && $_REQUEST["address"] !== '') ? htmlentities($_REQUEST["address"],ENT_QUOTES) : $res["address"]) . "',
	phone = '" . $phone . "',
	class = '" . ((isset($_REQUEST["class"]) && $_REQUEST["class"] !== '') ? htmlentities($_REQUEST["class"],ENT_QUOTES) : $res["class"]) . "',
	organisation = '" . ((isset($_REQUEST["organisation"]) && $_REQUEST["organisation"] !== '') ? htmlentities($_REQUEST["organisation"],ENT_QUOTES) : $res["organisation"]) . "',
	industry = '" . ((isset($_REQUEST["industry"]) && $_REQUEST["industry"] !== '') ? htmlentities($_REQUEST["industry"],ENT_QUOTES) : $res["industry"]) . "',
	positioninthecompany = '" . ((isset($_REQUEST["positioninthecompany"]) && $_REQUEST["positioninthecompany"] !== '') ? htmlentities($_REQUEST["positioninthecompany"],ENT_QUOTES) : $res["positioninthecompany"]) . "',
	currency = '" . ((isset($_REQUEST["currency"]) && $_REQUEST["currency"] !== '') ? htmlentities($_REQUEST["currency"],ENT_QUOTES) : $res["currency"]) . "',
	timezone = '" . ((isset($_REQUEST["timezone"]) && $_REQUEST["timezone"] !== '') ? htmlentities($_REQUEST["timezone"],ENT_QUOTES) : $res["timezone"]) . "',
	country = '" . ((isset($_REQUEST["country"]) && $_REQUEST["country"] !== '') ? htmlentities($_REQUEST["country"],ENT_QUOTES) : $res["country"]) . "'";
	
	//location_id = ". ((isset($_REQUEST["location_id"]) && intval($_REQUEST["location_id"]) > 0) ? intval($_REQUEST["location_id"]) : $res["location_id"]) .",
	//supervisoremail = '" . ((isset($_REQUEST["supervisoremail"]) && $_REQUEST["supervisoremail"] !== '') ? htmlentities($_REQUEST["supervisoremail"]) : $res["supervisoremail"]) . "',
	//supervisoremail2 = '" . ((isset($_REQUEST["supervisoremail2"]) && $_REQUEST["supervisoremail2"] !== '') ? htmlentities($_REQUEST["supervisoremail2"]) : $res["supervisoremail2"]) . "',
	//organisation_id = ". ((isset($_REQUEST["organisation_id"]) && intval($_REQUEST["organisation_id"]) > 0)  ? intval($_REQUEST["organisation_id"]) : $res["organisation_id"]). ",
	//permissions = '" . ((isset($_REQUEST["permissions"]) && $_REQUEST["permissions"] !== '' ) ? htmlentities($_REQUEST["permissions"]) : $res["permissions"]) . "'";	
	
// 	exit("Values: " . $query);
	$result = $user->updateUserDetails($query, $id);
	if($result){
		echo success($result,200, "Successful","Successful");
	}else{
		echo badRequest(204, "Not successful");
	}
	
}else{
    
    $hpassword = (isset($_POST["upw"]) && $_POST["upw"] !== "") ? hash('sha256', escape($_POST["upw"])) : "BAD";
    
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : "BAD";
    $phone = ((isset($_POST["phone"]) && is_numeric($_POST["phone"]) && strlen($_POST["phone"]) >= 10) ? $_POST["phone"] : "BAD");
    
    $referralcode = hash('sha256', $email);

	$fields = "email,lastname,firstname,othernames,imageurl,address,phone,upw,class,
	permissions,status,tlog,role,dateofbirth,online,referralcode";
	
	$values = "'" . $email . "',
	'" . ((isset($_REQUEST["lastname"]) && $_REQUEST["lastname"] !== '') ? htmlentities($_REQUEST["lastname"],ENT_QUOTES) : '-') . "',
	'" . ((isset($_REQUEST["firstname"]) && $_REQUEST["firstname"] !== '') ? htmlentities($_REQUEST["firstname"],ENT_QUOTES) : '-') . "',
	'" . ((isset($_REQUEST["othernames"]) && $_REQUEST["othernames"] !== '') ? htmlentities($_REQUEST["othernames"],ENT_QUOTES) : '-') . "',
	'" . $filename1 . "',
	'" . ((isset($_REQUEST["address"]) && $_REQUEST["address"] !== '') ? htmlentities($_REQUEST["address"],ENT_QUOTES) : '-') . "',
	'" . $phone . "',
	
	'" . $hpassword . "',
	'$becomeamerchant',	
	
	'" . ((isset($_REQUEST["permissions"]) && $_REQUEST["permissions"] !== '' )? htmlentities($_REQUEST["permissions"],ENT_QUOTES) : '-') . "',
	'NOT VERIFIED',
	'" . $_SESSION["user_id"] . " " . date('Y-m-d H:i:s') . "',
	'$becomeamerchant',
	'" . ((isset($_REQUEST["dateofbirth"]) && $_REQUEST["dateofbirth"] !== '' )? $_REQUEST["dateofbirth"] : '2000-01-01') . "',
	'NO','$referralcode'";
	
	//exit("Values: " . $values);
	
	if($hpassword === "BAD" || $email === "BAD" || $phone === "BAD"){
	    //exit("upw: " . $hpassword . " email: " . $email . " phone: " . $phone);
	    //exit(success("Invalid User",201, "Invalid user or password","Invalid user or password"));
	    exit(badRequest(204, "Bad credentials"));
	}else{
	    $result = $user->registerUser($fields, $values);
	}
	
	if($result){
		echo success($result,200, "Successful","Successful");
	}else{
	    echo badRequest(204, "Registration not successful");
		//echo success($result,204, "Failed: " . $result,"ERROR"); 
	}
    
    
}

?>

