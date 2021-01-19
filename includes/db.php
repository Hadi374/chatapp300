<?php

require_once "includes/config.php";

class Database {
    private $user = DB_USERNAME;
    private $pass = DB_PASSWORD;
    private $db = DB_DATABASE;
    private $host = DB_SERVER;
    
    private $handle;
    private $stmt;

    function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db;
        $options = array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

        try {
            $this->handle = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    function query($sql) {
        $this->stmt = $this->handle->prepare($sql);
    }

    function bind($param, $value) {
        $type = PDO::PARAM_STR;
        switch(true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
            break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
            break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
            break;
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    function execute() {
        if($this->stmt->execute()) {
            return $this->handle->lastInsertId();
        }
        return 0;
    }

    function ResultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>