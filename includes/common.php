<?php
// all codes of backend comes here.
//$db = new Database;

// TODO: separate public and private methods.


require_once 'includes/db.php';
require_once 'includes/utils.php';

class common {
    private $db;

    function __construct() {
        $this->db = new Database;
    }

    function hashPassword($password) {
        return hash("sha256", $password);
    }

    function validateEmail($email) {
        return mb_ereg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z]{2,3})$", $email);
    }

    function getFullUser($id) {
        $this->db->query("SELECT id,name,username,bio,profile,created_at FROM users WHERE id=:id");
        $this->db->bind(":id", $id);
        $result = $this->db->resultSet();
        if(count($result) == 1) {
            return $result[0];
        }
        failed("User not exist: " . $id);
    }

    function register($name,  $username, $email, $password) {
        echo "register " . $name . ' ' . $username . " " . $email . ' ' . $password . '<br>';
        return array(
            "test1" => 123,
            "test2" => "registered"
        );
    }

    function login($username, $password) {
        echo "login" . $username . ' ' . $password;
    }

}



?>