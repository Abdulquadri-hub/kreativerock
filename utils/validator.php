<?php

class Validator
{

    public $param;
    public $error = null;
    public $response = array();

    public function __construct($param)
    {
        $this->param = $param;
    }

    public function string($msg)
    {
        if (trim($this->param == "")) {
            $this->error = $msg;
            return $this;
        } else {
            $this->error = null;
            return $this;
        }
    }

    public function email()
    {
        if (!preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $this->param)) {
            $this->error = "Email is invalid";
            return $this;
        } else {
            $this->error = null;
            return $this;
        }
    }

    public function integer()
    {
        if (!preg_match('/[0-9]/', $this->param)) {
            $this->error = "Integer is invalid";
            return $this;
        } else {
            $this->error = null;
            return $this;
        }
    }

    public function exec_validate()
    {
        if ($this->error !== null) {
            $this->response["isvalid"] = true;
            $this->response["error"] = $this->error;
        } else {
            $this->response["isvalid"] = false;
            $this->response["error"] = $this->error;
        }
        return $this->response;
    }
}

/**
 * Init validation class
 * @param any param - body parameter
 * @return object - validator object
 */
function owi($param)
{
    return new Validator($param);
}

/**
 *
 * @param array schema - schema array
 * @return object - response object
 */
function validate($schema)
{
    $errors = [];
    for ($i = 0; $i < count($schema); $i++) {
        if ($schema[$i]["error"] != null)
            $errors[] = $schema[$i]["error"];
    }

    if (count($errors) > 0) {
        $response["isvalid"] = false;
        $response["errors"] = $errors;
    } else {
        $response["isvalid"] = true;
        $response["errors"] = $errors;
    }
    return $response;
};
