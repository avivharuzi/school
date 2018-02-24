<?php

class Database {
    private $Connection;
    private $Server      = "localhost";
    private $User        = "root";
    private $Password    = "";
    private $Db          = "school";
    private $IsConnected = false;
    private static $dbSingleInstance;
    
    private function __construct() {
        $this->Connection = new mysqli($this->Server, $this->User, $this->Password, $this->Db);
        
        if ($this->Connection->connect_errno) {
            $this->IsConnected = false;
            exit();
        } else {
            $this->IsConnected = true;
        }
    }
    
    public static function getConnectionSingleInstance() {
        if (!self::$dbSingleInstance) { 
            self::$dbSingleInstance = new Database();
        }
        return self::$dbSingleInstance;
    }
    
    public function connectData($sql) {
        $response = $this->Connection->query($sql);
        
        if ($response)  {
            return true;
        } else {
            return false;
        }
    }

    public function escape($value) {
        return $this->Connection->real_escape_string($value);
    }
    
    public function getSingleData($sql, $class = "stdClass") {
        $result = $this->Connection->query($sql);
        
        if ($result->num_rows == 1) {
            $response = $result->fetch_object($class);
        } else {
            return false;
        }
        return $response;
    }

    public function getFullData($sql, $class = "stdClass") {
        $result = $this->Connection->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object($class)) {
                $response[] = $row;
            }
        } else {
            return false;
        }
        return $response;
    }
    
    public function getRows($sql) {
        $result = $this->Connection->query($sql);
        
        if ($result) {
            return $result->num_rows;
        } else {
            return false;
        }
    }

    public function getId() {
        return $this->Connection->insert_id;
    }

    public function disconnect() {
        $this->connection->close();
    }
}

$GLOBALS["conn"] = Database::getConnectionSingleInstance();

?>