<?php

/**
 * Undocumented function
 *
 * @param [type] $data
 * @param integer $code
 * @param string $message
 * @return void
 */
function success($data, $code = 200, $message = 'success',$result='Successful')
{
    $res['status'] = true;
    $res['code'] = $code;
    $res['message'] = $message;
    $res['data'] = $data;
    $res['result'] = $result;
    return json_encode($res);
}

/**
 * Undocumented function
 *
 * @param [type] $data
 * @param integer $code
 * @param string $message
 * @return void
 */
function error($data, $code = 500, $message = 'error',$result='Failed')
{
    $res['status'] = false;
    $res['code'] = $code;
    $res['message'] = $message;
    $res['data'] = $data;
    $res['result'] = $result;
    return json_encode($res);
}

/**
 * Undocumented function
 *
 * @param [type] $data
 * @param [type] $curpage
 * @param [type] $total
 * @param [type] $totalpages
 * @param integer $code
 * @param string $message
 * @return void
 */
function paginatedSuccess($data)
{
    $res['status'] = true;
    $res['code'] = 200;
    $res['total'] = $data["total"];
    $res['currentpage'] = $data["currentpage"];
    $res['totalpages'] = $data["totalpages"];
    $res['data'] = $data["data"];
    return json_encode($res);
}

/**
 * Undocumented function
 *
 * @param integer $code
 * @param string $message
 * @return void
 */
function successMessage($code = 200, $message = 'success')
{
    $res['status'] = true;
    $res['code'] = $code;
    $res['message'] = $message;
    return json_encode($res);
}


function message($code = 200, $type = "true",  $message)
{
    $res = [];
    if($type == "false")
    {
        $res['status'] = false;
        $res['code'] = $code;
        $res['message'] = $message;
        
    }elseif($type == "true") {
        
        $res['status'] = true;
        $res['code'] = $code;
        $res['message'] = $message;
    }
    
    return json_encode($res);
}

/**
 * Undocumented function
 *
 * @param integer $code
 * @param string $message
 * @return void
 */
function badRequest($code = 400, $message = 'error')
{
    $res['status'] = false;
    $res['code'] = $code;
    $res['message'] = $message;
    return json_encode($res);
}

/**
 * Undocumented function
 *
 * @param integer $code
 * @param [type] $errors
 * @return void
 */
function validationError($code = 400, $errors)
{
    $res['status'] = false;
    $res['code'] = $code;
    $res['errors'] = $errors;

    return json_encode($res);
}
