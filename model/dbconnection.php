<?php
class DBConnection{
    
    // properties
    private $host = 'localhost';
    private $dbname = "db_kreativerock";
    private $dbuser = "root";
    private $dbpass = "";

    // methods
    public function __construct(){}
	public function openConnection(){
        $dbconn = new mysqli($this->host, $this->dbuser, $this->dbpass, $this->dbname);
        return $dbconn;
        		
    }
	public function closeConnection($db){
		mysqli_close($db);
	}
}
?>