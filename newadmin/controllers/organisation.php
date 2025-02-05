<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

date_default_timezone_set('Africa/Lagos');

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    $useremail = filter_var($_POST["contactpersonemail"], FILTER_VALIDATE_EMAIL) ? $_POST["contactpersonemail"] : "BAD";
    if($useremail === "BAD"){
        exit(badRequest(204,'Bad contact person email'));
    }
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
}else{
    $useremail = $_SESSION['elfuseremail'];
}

if($_FILES["userphotoname"]["name"] !== "" && $_FILES["userphotoname"]["name"] !== null && $_FILES["userphotoname"]["name"] !== "-"){
	$filenamearray = explode(".",$_FILES["userphotoname"]["name"]); 
	$filename1 = $filenamearray[0] . mt_rand(10,111111111) . "." . end($filenamearray);
}else { $filename1 = "-"; }

$returnvalue = '';
if($_POST["photofilename"] !== null && $_POST["photofilename"] !== "-"){	
	$allowedExts = array("gif", "jpeg", "jpg","pdf","png");
	$temp = explode(".", $_FILES["userphotoname"]["name"]);
	$returnvalue .= '' . $_FILES["userphotoname"]["name"];
	$extension = end($temp);
	if ((($_FILES["userphotoname"]["type"] === "image/gif") || ($_FILES["userphotoname"]["type"] === "image/jpeg") || ($_FILES["userphotoname"]["type"] === "image/jpg") || ($_FILES["userphotoname"]["type"] === "image/pjpeg") || ($_FILES["userphotoname"]["type"] === "image/png") || ($_FILES["userphotoname"]["type"] === "application/pdf")) && in_array($extension, $allowedExts)){
		if ($_FILES["userphotoname"]["error"] > 0){
			$returnvalue .= ' | error saving photo'; //echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
		}else{
			/*
			if (file_exists("../images/borrowers/" . $_FILES["userphotoname"]["name"])){
				$returnvalue .= ' | the file exists'; //echo $_FILES["userphoto"]["name"] . " already exists. ";
  		    }else{ */
				move_uploaded_file($_FILES["userphotoname"]["tmp_name"],"../images/" . $filename1);
				$returnvalue .= "Upload Successful";
  		    //}
		}
	}else{
         $returnvalue .= ' | Invalid file type';	    
	}
}

$organisationname = ((isset($_POST["organisationname"]) && $_POST["organisationname"] !== "") ? htmlentities($_POST["organisationname"],ENT_QUOTES) : "-" );
$email = ((isset($_POST["email"]) && $_POST["email"] !== "") ? htmlentities($_POST["email"],ENT_QUOTES) : "-" );
$owner = ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? htmlentities($_POST["owner"],ENT_QUOTES) : "-" );
$categoryid = ((isset($_POST["categoryid"]) && $_POST["categoryid"] > 0) ? intval($_POST["categoryid"]) : 0 );

if($organisationname === "-" || $email === "-"){
    exit(badRequest(204, "Not successful. Invalid Organisation name or email"));
}
if($owner === "-"){
    //exit(badRequest(204, "Not successful. Owner not set"));
}
$framework = new FrameWork();
$res = $framework->getOrganisationInfo("id = " . intval($_POST['id']));

if($res){
    $id = $res['id'];
    $query = "
        owner = '" . ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? htmlentities($_POST["owner"],ENT_QUOTES) : $res['owner'] ) . "',
        phone = '" . ((isset($_POST["phone"]) && $_POST["phone"] !== "") ? htmlentities($_POST["phone"],ENT_QUOTES) : $res['phone'] ) . "',
        
        email = '" . ((isset($_POST["email"]) && $_POST["email"] !== "") ? htmlentities($_POST["email"],ENT_QUOTES) : $res['email'] ) . "',
        contactperson = '" . ((isset($_POST["contactperson"]) && $_POST["contactperson"] !== "") ? htmlentities($_POST["contactperson"],ENT_QUOTES) : $res['contactperson'] ) . "',
        contactpersonemail = '" . ((isset($_POST["contactpersonemail"]) && $_POST["contactpersonemail"] !== "") ? htmlentities($_POST["contactpersonemail"],ENT_QUOTES) : $res['contactpersonemail'] ) . "',
        facebook = '" . ((isset($_POST["facebook"]) && $_POST["facebook"] !== "") ? htmlentities($_POST["facebook"],ENT_QUOTES) : $res['facebook'] ) . "',
        xlink = '" . ((isset($_POST["xlink"]) && $_POST["xlink"] !== "") ? htmlentities($_POST["xlink"],ENT_QUOTES) : $res['xlink'] ) . "',
        instagram = '" . ((isset($_POST["instagram"]) && $_POST["instagram"] !== "") ? htmlentities($_POST["instagram"],ENT_QUOTES) : $res['instagram'] ) . "',
        youtube = '" . ((isset($_POST["youtube"]) && $_POST["youtube"] !== "") ? htmlentities($_POST["youtube"],ENT_QUOTES) : $res['youtube'] ) . "',
        linkedin = '" . ((isset($_POST["linkedin"]) && $_POST["linkedin"] !== "") ? htmlentities($_POST["linkedin"],ENT_QUOTES) : $res['linkedin'] ) . "',
        organisationname = '" . ((isset($_POST["organisationname"]) && $_POST["organisationname"] !== "") ? htmlentities($_POST["organisationname"],ENT_QUOTES) : $res['organisationname'] ) . "',
        state = '" . ((isset($_POST["state"]) && $_POST["state"] !== "") ? htmlentities($_POST["state"],ENT_QUOTES) : $res['state'] ) . "',
        country = '" . ((isset($_POST["country"]) && $_POST["country"] !== "") ? htmlentities($_POST["country"],ENT_QUOTES) : $res['country'] ) . "',
        currency = '" . ((isset($_POST["currency"]) && $_POST["currency"] !== "") ? htmlentities($_POST["currency"],ENT_QUOTES) : $res['currency'] ) . "',
        description = '" . ((isset($_POST["description"]) && $_POST["description"] !== "") ? htmlentities($_POST["description"],ENT_QUOTES) : $res['description'] ) . "',
        bankname = '" . ((isset($_POST["bankname"]) && $_POST["bankname"] !== "") ? htmlentities($_POST["bankname"],ENT_QUOTES) : $res['bankname'] ) . "',        
        accountnumber = '" . ((isset($_POST["accountnumber"]) && $_POST["accountnumber"] !== "") ? htmlentities($_POST["accountnumber"],ENT_QUOTES) : $res['accountnumber'] ) . "',                
        accountname = '" . ((isset($_POST["accountname"]) && $_POST["accountname"] !== "") ? htmlentities($_POST["accountname"],ENT_QUOTES) : $res['accountname'] ) . "',
        swiftcode = '" . ((isset($_POST["swiftcode"]) && $_POST["swiftcode"] !== "") ? htmlentities($_POST["swiftcode"],ENT_QUOTES) : $res['swiftcode'] ) . "',
        logo = '" . ((isset($filename1) && $filename1 !== "-") ? $filename1 : $res['logo'] ) . "',
        address = '" . ((isset($_POST["address"]) && $_POST["address"] !== "") ? htmlentities($_POST["address"],ENT_QUOTES) : $res['address'] ) . "'";

    $result = $framework->updateOrganisationDetails($query, $id);

    if ($result) {
        echo success($result, 200, "Successful", "Successful");
    }else {
        echo badRequest(204, "Not successful");
    }
    
}else{
    
    // fields names
    $fields = "owner,logo,phone,address,email,contactperson,contactpersonemail,facebook,xlink,instagram,youtube,linkedin,organisationname,
    state,country,currency,description,bankname,accountnumber,accountname,swiftcode,status,tlog,user";
    $values = "
    '" . ((isset($_POST["owner"]) && $_POST["owner"] !== "") ? htmlentities($_POST["owner"],ENT_QUOTES) : "-" ) . "',
    '$filename1',
    '" . ((isset($_POST["phone"]) && $_POST["phone"] !== "") ? htmlentities($_POST["phone"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["address"]) && $_POST["address"] !== "") ? htmlentities($_POST["address"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["email"]) && $_POST["email"] !== "") ? htmlentities($_POST["email"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["contactperson"]) && $_POST["contactperson"] !== "") ? htmlentities($_POST["contactperson"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["contactpersonemail"]) && $_POST["contactpersonemail"] !== "") ? htmlentities($_POST["contactpersonemail"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["facebook"]) && $_POST["facebook"] !== "") ? htmlentities($_POST["facebook"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["xlink"]) && $_POST["xlink"] !== "") ? htmlentities($_POST["xlink"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["instagram"]) && $_POST["instagram"] !== "") ? htmlentities($_POST["instagram"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["youtube"]) && $_POST["youtube"] !== "") ? htmlentities($_POST["youtube"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["linkedin"]) && $_POST["linkedin"] !== "") ? htmlentities($_POST["linkedin"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["organisationname"]) && $_POST["organisationname"] !== "") ? htmlentities($_POST["organisationname"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["state"]) && $_POST["state"] !== "") ? htmlentities($_POST["state"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["country"]) && $_POST["country"] !== "") ? htmlentities($_POST["country"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["currency"]) && $_POST["currency"] !== "") ? htmlentities($_POST["currency"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["description"]) && $_POST["description"] !== "") ? htmlentities($_POST["description"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["bankname"]) && $_POST["bankname"] !== "") ? htmlentities($_POST["bankname"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["accountnumber"]) && $_POST["accountnumber"] !== "") ? htmlentities($_POST["accountnumber"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["accountname"]) && $_POST["accountname"] !== "") ? htmlentities($_POST["accountname"],ENT_QUOTES) : "-" ) . "',
    '" . ((isset($_POST["swiftcode"]) && $_POST["swiftcode"] !== "") ? htmlentities($_POST["swiftcode"],ENT_QUOTES) : "-" ) . "',
    'PENDING APPROVAL',
    '" . $useremail . " | " . date("Y-m-d H:i:s") . "','$useremail'";

    $result = $framework->registerOrganisation($fields, $values);
    if($result){
        echo success($result, 200, "Successful", "Successful");
    }else{
        echo badRequest(204, "Not successful");
    }
}
