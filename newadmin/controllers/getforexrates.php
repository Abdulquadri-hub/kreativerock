<?php
session_start();  
require_once '../../utils/errorhandler.php';
require_once '../../utils/response.php';
require_once '../../model/dbclass.php';
require_once '../../model/model.php';
require_once '../../model/FrameWork.php';

$framework = new FrameWork();
$results = $framework->executeByQuerySelector("SELECT * FROM markup");
//$jresult = json_encode($results);
$appid = "24f258b1c1824b128e17f341f558d0ff";

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://openexchangerates.org/api/latest.json?app_id=$appid",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => array(
      "accept: application/json"
    ),
));

$result = curl_exec($curl);
//$err = curl_error($curl);

$forex = json_encode(array("forex" => json_decode($result,true),"markup" => $results));
echo $forex;
    
?>