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

    function getSelf() {
        if(isset($_SESSION['id']))
            return $this->getFullUser($_SESSION['id']);
        failed("Please login first.");
    }

    function register($name,  $username, $email, $password) {
        if(empty($username) || empty($name)) {
            failed("Empty name or username");
        }
        // check if username is used by another user.
        $this->db->query("SELECT * FROM users WHERE username=:username");
        $this->db->bind(":username", $username);
        $result = $this->db->resultSet();
        if(count($result) == 1) {
            failed("Username Exists. choose another username or login");
        }

        if(!$this->validateEmail($email)) {
            failed("invalid Email");
        }

        $password = $this->hashPassword($password);
        //echo $hashed_password;

        $this->db->query("INSERT INTO users(name,username,email,password) VALUES(:name,:username,:email,:password)");
        
        $this->db->bind('name', $name);
        $this->db->bind('username', $username);
        $this->db->bind('email', $email);
        $this->db->bind('password', $password);
        
        $id = $this->db->execute();
        if($id == 0) {
            failed("Cannot create account.");
        }  

        $_SESSION['id'] = $id;
        $result = $this->getSelf();
            
        return $result; //success
    }

    function login($username, $password) {
        echo "login" . $username . ' ' . $password;
    }

}



?>