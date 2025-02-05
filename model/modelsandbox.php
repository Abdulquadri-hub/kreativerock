<?php
class ModelSandBox extends DBClassB
{
    /**
     * Database connection object
     *
     * @var object
     */
    public $conn;

    public function __construct()
    {
        $this->conn = parent::__construct();
    }


    /**
     * findOne
     *
     * Gets a single record from a database table
     * 
     * @param string $tablename - target table name
     * @param string $condition - search condition
     * @param string $fields - optional param for specifying fields to return.if not passed, all fields will be returned
     * @return object - the response object
     */
    public function findOne($tablename, $condition, $fields = "*")
    {
        $query = "SELECT $fields FROM $tablename WHERE $condition LIMIT 1";
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return count($data) > 0 ? $data[0] : null;
    }

    /**
     * findAll
     *
     * Gets an all records from a database table
     * 
     * @param string $tabelename - target table name
     * @param string $fields - optional param for specifying fields to return.if not passed, all fields will be returned
     * @return array - the response array of objects
     */
    public function findAll($tablename, $fields = "*")
    {
        $query = "SELECT $fields FROM $tablename";
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * findAllWhere
     *
     * Gets an array of record from a database table with a given condition
     * @param string $tabelename - target table name
     * @param mixed $condition - search condition
     * @param string $fields - optional param for specifying fields to return.if not passed, all fields will be returned
     * @return array - the response array of objects
     */
    public function findAllWhere($tablename, $condition = 1, $fields = "*")
    {
        $query = "SELECT $fields FROM $tablename WHERE $condition";
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row; //$result->fetch_all(MYSQLI_ASSOC);
            }
        }
        return $data;
    }

    /**
     * insertgetid
     *
     * insert data to a table
     * @param string $tabelename - target table name
     * @param string $fields - Fields to insert into e.g "name, age ..."
     * @param string $values - Values to be inserted into field e.g "'John', '20' ..."
     * @return boolean - true or false
     */
    public function insertgetid($tablename, $fields, $values)
    {
        $query = "INSERT INTO $tablename ($fields) VALUES($values)";
        echo $query;
        // exit($query);
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        if($result){
            $last_id = $this->conn->insert_id;
            return $last_id;
        }else{
            return null;
        }
    }

    /**
     * insertdata
     *
     * insert data to a table
     * @param string $tabelename - target table name
     * @param string $fields - Fields to insert into e.g "name, age ..."
     * @param string $values - Values to be inserted into field e.g "'John', '20' ..."
     * @return boolean - true or false
     */
    public function insertdata($tablename, $fields, $values)
    {
        $query = "INSERT INTO $tablename ($fields) VALUES($values)";
        // exit($query);
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        return $result;
    }

    /**
     * Update
     *
     * @param string $tabelename
     * @param string $query
     * @param string $condition
     * @return void
     */
    public function update($tablename, $query, $condition)
    {
        $sql = "UPDATE $tablename SET " . $query . " " . $condition;
        $result = $this->conn->query($sql) or errorhandler($this->conn->error);
        return $result;
    }

    /**
     * Delete
     *
     * @param string $tabelename
     * @param string $condition
     * @return void
     */
    public function deletedata($tablename, $condition)
    {
        $check = $this->findOne($tablename, $condition);
        if ($check === null) return $check;
        $query = "DELETE FROM $tablename WHERE $condition";
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        return $result;
    }

    public function getCount($tablename, $condition)
    {
        $query = "SELECT * FROM $tablename WHERE $condition";
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        return $result->num_rows;
    }

    /**
     * Paginate
     *
     * @param string $tabelename
     * @param integer $pageno
     * @param integer $limit
     * @param string $fields
     * @param integer $condition
     * @return void
     */
    public function paginate($tablename, $condition = 1, $pageno, $limit, $fields = "*")
    {
        $total = $this->getCount($tablename, $condition);
        $offset = ($pageno - 1) * $limit;
        $query = "SELECT $fields FROM $tablename WHERE $condition LIMIT $limit OFFSET $offset";
        //$result = $this->exec_query("SELECT $fields FROM $tabelename WHERE $condition LIMIT $limit OFFSET $offset");
        $result = $this->conn->query($query) or errorhandler($this->conn->error);

        $res = array();
        $res['total'] = $total;
        $res['currentpage'] = $pageno;
        $res['totalpages'] = ceil($total / $limit);
        if($result){
            $res["data"] = $result -> fetch_all(MYSQLI_ASSOC);
        }else{
            $res["data"]="";
        }
        return $res;
    }

    public function lastId()
    {
        return $this->conn->insert_id;
    }

    /**
     * Escape string
     *
     * @param string param
     * @return string
     */
    public function escapeString($param)
    {
        return $this->conn->real_escape_string($param);
    }

    /**
     * Execute custom query
     *
     * @param string $query
     * @return void
     */
    public function exec_query($query)
    {
        $result = $this->conn->query($query) or errorhandler($this->conn->error);
        $type = explode(" ", $query)[0];
        if ($type === "SELECT" || $type === "select") {
            $data = array();
            if ($result) {

                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }
        return $result;
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}
