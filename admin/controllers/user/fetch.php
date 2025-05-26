<?php
session_start();
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

if ($_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
    exit(badRequest(204,'Invalid session data. Proceed to login'));
}

$user = new User();

$userId = isset($_GET['user_id']) &&  $_GET['user_id'] !== "" ?  $_GET['user_id'] : "";

if($userId !== ""){
       $userarray = $user->getUser($userId);
    
    if(!empty($userarray)) {
        echo success($userarray, 200, "Successful", "User retrieved successfully");
    } else {
        echo badRequest(204, 'There may be no users with matching criteria');
    } 
}else{
    $userarray = $user->getAllUsers();
    
    if(!empty($userarray)) {
        echo success($userarray, 200, "Successful", "Users retrieved successfully");
    } else {
        echo badRequest(204, 'There may be no users with matching criteria');
    }
}

