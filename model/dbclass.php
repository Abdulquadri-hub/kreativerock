<?php

class DBClass
{
    private $host = 'localhost';
    private $dbname = "db_kreativerock";
    private $dbuser = "root";
    private $dbpass = "";
    

    public function __construct()
    {
        $dbconn = new mysqli($this->host, $this->dbuser, $this->dbpass, $this->dbname);
        return $dbconn;
    }
}

?>