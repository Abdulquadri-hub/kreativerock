<?php

class dbFunctions extends DBClass {
    
    private $connection;
    private $logger;
    
    public function __construct()
    {
        $this->connection = parent::__construct();
        $this->logger = new Logger("error_log"); 
    }
    
    public function insert($table, $data) {

        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $types = str_repeat("s", count($data));
        $values = array_values($data);
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        if (!$stmt = $this->connection->prepare($sql)) {
            $this->logger->error('MySQL prepare error: ' . $this->connection->connect_error);
            return false;
        }
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return $this->connection->insert_id;
        } else {
            echo $this->connection->error;
            return false;
        }
    }

    public function select($table, $columns = "*", $where = null, $orderBy = null, $limit = null) {
        $sql = "SELECT $columns FROM $table";

        if ($where !== null) {
            $sql .= " WHERE $where";
        }

        if ($orderBy !== null) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }

        $result = $this->connection->query($sql);
        

        if ($result === false) {
            $this->connection->error;
            return false;
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function selectSum($table, $column, $where = '') {
        $sql = "SELECT SUM($column) as total FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }

        $result = $this->connection->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'] ? $row['total'] : 0;
        }
        return 0;
    }
    
    public function find($table, $where = null, $orderBy = null, $limit = null){
        $sql = "SELECT * FROM $table";

        if ($where !== null) {
            $sql .= " WHERE $where";
        }

        if ($orderBy !== null) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }

        $result = $this->connection->query($sql);

        if ($result === false) {
            return false;
        }

        $data =  $result->fetch_all(MYSQLI_ASSOC);
        return count($data) > 0 ? $data[0] : null;
    }

    public function update($table, $data, $where) {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        $set = implode(", ", $set);

        $sql = "UPDATE $table SET $set WHERE $where";
        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            $this->logger->error('Prepare failed: ' . $this->connection->error);
            return false;
        }

        $types = str_repeat("s", count($data));
        $values = array_values($data);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $this->logger->info('Update Successfull: ' . $this->connection->insert_id);
            return true;
        } else {
            $this->logger->error('Update failed: ' . $this->connection->error);
            return false;
        }
    }

    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->connection->query($sql);
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        
        if ($stmt === false) {
            echo 'Prepare failed: ' . $this->connection->error;
            return false;
        }

        if (!empty($params)) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : error_log("No result set returned: " . $stmt->error); true;
        } else {
            echo 'Execute failed: ' . $stmt->error;
            return false;
        }
    }

    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    public function commitTransaction() {
        $this->connection->commit();
    }

    public function rollbackTransaction() {
        $this->connection->rollback();
    }

    public function __destruct() {
        $this->connection->close();
    }
}