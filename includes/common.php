<?php
// all codes of backend comes here.
//$db = new Database;

require_once 'includes/db.php';

class common {
    private $db;

    function __construct() {
        echo 'before database';
        $this->db = new Database;
    }

    function register($name,  $username, $email, $password) {
        echo "register " . $name . ' ' . $username . " " . $email . ' ' . $password;
    }

    function login($username, $password) {
        echo "login" . $username . ' ' . $password;
    }

}



?>